#include "BTreeNode.h"
#include <string.h>
#include <stdlib.h>

using namespace std;

/* Leaf node constructor */
BTLeafNode::BTLeafNode()
{
	//set buffer (1024 byte array) initially to 0
	memset(buffer,0,PageFile::PAGE_SIZE);
	keyCount = 0;
}

/*
 * Read the content of the node from the page pid in the PageFile pf.
 * @param pid[IN] the PageId to read
 * @param pf[IN] PageFile to read from
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::read(PageId pid, const PageFile& pf)
{
	return pf.read(pid, buffer);
}
    
/*
 * Write the content of the node to the page pid in the PageFile pf.
 * @param pid[IN] the PageId to write to
 * @param pf[IN] PageFile to write to
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::write(PageId pid, PageFile& pf)
{ 
	return pf.write(pid, buffer);
}

/*
 * Return the number of keys stored in the node.
 * @return the number of keys in the node
 */
int BTLeafNode::getKeyCount()
{
	int pairSize = sizeof(int) + sizeof(RecordId);
	int maxIndex = PageFile::PAGE_SIZE - sizeof(PageId);	//last 4 bytes = PageId to next leaf node
	int count = 0;
	int i, curKey;
	char* temp = buffer;

	for(i = 0; i < maxIndex; i += pairSize)
	{
		memcpy(&curKey, temp, sizeof(int)); 
		if(curKey == 0)
			break;
		count++;
		
		temp += pairSize;
	}
	return count;
}

/*
 * Insert a (key, rid) pair to the node.
 * @param key[IN] the key to insert
 * @param rid[IN] the RecordId to insert
 * @return 0 if successful. Return an error code if the node is full.
 */
RC BTLeafNode::insert(int key, const RecordId& rid)
{
	PageId nextNodePtr = getNextNodePtr();	//last 4 bytes for inserted leaf reconstruction
	int pairSize = sizeof(int) + sizeof(RecordId);
	int maxEntry = (PageFile::PAGE_SIZE - sizeof(PageId))/pairSize;
	int maxIndex = PageFile::PAGE_SIZE - sizeof(PageId);
	int i, curKey;
	char* temp = buffer;
 
	if (getKeyCount() >= maxEntry)
	{
		return RC_NODE_FULL;
	}

	for(i = 0; i < maxIndex; i += pairSize)
	{
		memcpy(&curKey, temp, sizeof(int)); 
		if( (key < curKey) || (curKey == 0) )
			break;
		
		temp += pairSize;
	}

	char* newBuffer = (char*)malloc(PageFile::PAGE_SIZE);
	memset(newBuffer, 0, PageFile::PAGE_SIZE);

	memcpy(newBuffer, buffer, i);	//copy values upto i

	PageId pid = rid.pid;	//values of new pair (to be inserted)
	int sid = rid.sid;

	memcpy(newBuffer + i, &key, sizeof(int));
	memcpy(newBuffer + i + sizeof(int), &rid, sizeof(RecordId));

	memcpy(newBuffer + i + pairSize, buffer + i, getKeyCount() * pairSize - i);	//copy rest of values
	memcpy(newBuffer + maxIndex, &nextNodePtr, sizeof(PageId));

	memcpy(buffer, newBuffer, PageFile::PAGE_SIZE);	//copy and free newBuffer
	free(newBuffer);

	keyCount++;	//insert success; increment key count

	return 0;
}

/*
 * Insert the (key, rid) pair to the node
 * and split the node half and half with sibling.
 * The first key of the sibling node is returned in siblingKey.
 * @param key[IN] the key to insert.
 * @param rid[IN] the RecordId to insert.
 * @param sibling[IN] the sibling node to split with. This node MUST be EMPTY when this function is called.
 * @param siblingKey[OUT] the first key in the sibling node after split.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::insertAndSplit(int key, const RecordId& rid, 
                              BTLeafNode& sibling, int& siblingKey)
{
	PageId nextNodePtr = getNextNodePtr();	//last 4 bytes for inserted leaf reconstruction
	int pairSize = sizeof(int) + sizeof(RecordId);
	int maxEntry = (PageFile::PAGE_SIZE - sizeof(PageId))/pairSize;
	int maxIndex = PageFile::PAGE_SIZE - sizeof(PageId);

	if(getKeyCount() < maxEntry)
	{
		return RC_INVALID_FILE_FORMAT;
	}	

	memset(sibling.buffer, 0, PageFile::PAGE_SIZE);

	int halfKeyCount = ( (int)((getKeyCount()+1)/2) );	//keys to remain in 1st half
	int splitIndex = halfKeyCount * pairSize;	//index to split
	
	memcpy(sibling.buffer, buffer + splitIndex, maxIndex - splitIndex);	//copy right half from split

	sibling.keyCount = getKeyCount() - halfKeyCount;	//sibling's key count
	sibling.setNextNodePtr(getNextNodePtr());	//current node's pointer

	memset(buffer + splitIndex, 0, maxIndex - splitIndex);	//2nd half
	keyCount = halfKeyCount;

	int firstHalfKey;
	memcpy(&firstHalfKey, sibling.buffer, sizeof(int));

	if(key < firstHalfKey)	//insert pair
	{
		insert(key, rid);
	}
	else
	{
		sibling.insert(key,rid);
	}

	//Check which buffer to insert new (key, rid) into
	memcpy(&firstHalfKey, sibling.buffer, sizeof(int));
	
	//Insert pair and increment number of keys
	if(key>=firstHalfKey) //If our key belongs in the second buffer (since it's sorted)...
	{
		sibling.insert(key, rid);
	}
	else //Otherwise, place it in the first half
	{
		insert(key, rid);
	}
	
	memcpy(&siblingKey, sibling.buffer, sizeof(int));	//copy sibling first key & rid
	
	RecordId siblingRid;
	siblingRid.pid = -1;
	siblingRid.sid = -1;
	memcpy(&siblingRid, sibling.buffer + sizeof(int), sizeof(RecordId));
	
	return 0;
}

/**
 * If searchKey exists in the node, set eid to the index entry
 * with searchKey and return 0. If not, set eid to the index entry
 * immediately after the largest index key that is smaller than searchKey,
 * and return the error code RC_NO_SUCH_RECORD.
 * Remember that keys inside a B+tree node are always kept sorted.
 * @param searchKey[IN] the key to search for.
 * @param eid[OUT] the index entry number with searchKey or immediately
                   behind the largest key smaller than searchKey.
 * @return 0 if searchKey is found. Otherwise return an error code.
 */
RC BTLeafNode::locate(int searchKey, int& eid)
{
	int pairSize = sizeof(int) + sizeof(RecordId);
	int i, curKey;
	char* temp = buffer;

	for(i = 0; i < getKeyCount() * pairSize; i++)
	{
		memcpy(&curKey, temp, sizeof(int));

		if(curKey >= searchKey)
		{
			eid = i;
			return 0;
		}

		temp += pairSize;
	}

	return RC_NO_SUCH_RECORD;
}

/*
 * Read the (key, rid) pair from the eid entry.
 * @param eid[IN] the entry number to read the (key, rid) pair from
 * @param key[OUT] the key from the entry
 * @param rid[OUT] the RecordId from the entry
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::readEntry(int eid, int& key, RecordId& rid)
{
	int pairSize = sizeof(int) + sizeof(RecordId);
	int entryPos = eid * pairSize;
	char* temp = buffer;

	if (eid < 0 || eid >= getKeyCount())
	{
		return RC_INVALID_CURSOR;
	}

	memcpy(&key, temp + entryPos, sizeof(int));
	memcpy(&rid, temp + entryPos + sizeof(int), sizeof(RecordId));
	
	return 0;
}

/*
 * Return the pid of the next sibling node.
 * @return the PageId of the next sibling node 
 */
PageId BTLeafNode::getNextNodePtr()
{
	PageId pid = 0;	//assume no next node
	int maxIndex = PageFile::PAGE_SIZE - sizeof(PageId);
	char* temp = buffer;

	memcpy(&pid, temp + maxIndex, sizeof(PageId));	//last PageID of buffer
	
	return pid;
}

/*
 * Set the pid of the next sibling node.
 * @param pid[IN] the PageId of the next sibling node 
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::setNextNodePtr(PageId pid)
{
	int maxIndex = PageFile::PAGE_SIZE - sizeof(PageId);
	char* temp = buffer;
	
	memcpy(temp + maxIndex, &pid, sizeof(PageId));	//copy pid
	
	return 0;
}

/* ===================== NON LEAF ===================== */

/* Nonleaf node constructor */
BTNonLeafNode::BTNonLeafNode()
{
	//set buffer (1024 byte array) initially to 0
	memset(buffer,0,PageFile::PAGE_SIZE);
	keyCount = 0;
}

/*
 * Read the content of the node from the page pid in the PageFile pf.
 * @param pid[IN] the PageId to read
 * @param pf[IN] PageFile to read from
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::read(PageId pid, const PageFile& pf)
{
	return pf.read(pid, buffer);
}
    
/*
 * Write the content of the node to the page pid in the PageFile pf.
 * @param pid[IN] the PageId to write to
 * @param pf[IN] PageFile to write to
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::write(PageId pid, PageFile& pf)
{ 
	return pf.write(pid, buffer);
}

/*
 * Return the number of keys stored in the node.
 * @return the number of keys in the node
 */
int BTNonLeafNode::getKeyCount()
{
	int pairSize = sizeof(int) + sizeof(RecordId);
	int maxIndex = PageFile::PAGE_SIZE;
	int count = 0;
	int i, curKey;
	char* temp = buffer + 8;	//skip first PageId (8 bytes)

	for(i = 8; i < maxIndex; i += pairSize)
	{
		memcpy(&curKey, temp, sizeof(int));
		if(curKey == 0)
			break;
		count++;

		temp += pairSize;
	}
	return count;
}

/*
 * Insert a (key, pid) pair to the node.
 * @param key[IN] the key to insert
 * @param pid[IN] the PageId to insert
 * @return 0 if successful. Return an error code if the node is full.
 */
RC BTNonLeafNode::insert(int key, PageId pid)
{
	int pairSize = sizeof(int) + sizeof(RecordId);
	int maxEntry = (PageFile::PAGE_SIZE - sizeof(PageId))/pairSize;
	int maxIndex = PageFile::PAGE_SIZE;
	int i, curKey;
	char* temp = buffer + 8;	//skip first PageId (8 bytes)
	
	if (getKeyCount() >= maxEntry)
	{
		return RC_NODE_FULL;
	}

	for(i = 8; i < maxIndex; i += pairSize)
	{
		memcpy(&curKey, temp, sizeof(int));
		if( (key < curKey) || (curKey == 0) )
			break;

		temp += pairSize;
	}

	char* newBuffer = (char*)malloc(PageFile::PAGE_SIZE);
	memset(newBuffer, 0, PageFile::PAGE_SIZE);

	memcpy(newBuffer, buffer, i);	//copy values upto i

	memcpy(newBuffer + i, &key, sizeof(int));
	memcpy(newBuffer + i + sizeof(int), &pid, sizeof(PageId));

	memcpy(newBuffer + i + pairSize, buffer + i, getKeyCount() * pairSize - i + 8);	//copy rest of values
	
	memcpy(buffer, newBuffer, PageFile::PAGE_SIZE);	//copy and free newBuffer
	free(newBuffer);
	
	keyCount++;	//insert success; increment key count

	return 0;
}

/*
 * Insert the (key, pid) pair to the node
 * and split the node half and half with sibling.
 * The middle key after the split is returned in midKey.
 * @param key[IN] the key to insert
 * @param pid[IN] the PageId to insert
 * @param sibling[IN] the sibling node to split with. This node MUST be empty when this function is called.
 * @param midKey[OUT] the key in the middle after the split. This key should be inserted to the parent node.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::insertAndSplit(int key, PageId pid, BTNonLeafNode& sibling, int& midKey)
{
	int pairSize = sizeof(int) + sizeof(RecordId);
	int maxEntry = (PageFile::PAGE_SIZE - sizeof(PageId))/pairSize;
	int maxIndex = PageFile::PAGE_SIZE;
	
	if(getKeyCount() < maxEntry)
	{
		return RC_INVALID_FILE_FORMAT;
	}	

	memset(sibling.buffer, 0, maxIndex);

	int halfKeyCount = ( (int)((getKeyCount()+1)/2) );	//keys to remain in 1st half
	int splitIndex = halfKeyCount * pairSize + 8;	//index to split (+ first PageID offset)

	int lastFirstkey, firstLastkey;	//last key of 1st half & first key of 2nd half
	memcpy(&lastFirstkey, buffer + splitIndex - 8, sizeof(int));
	memcpy(&firstLastkey, buffer + splitIndex, sizeof(int));

	if(key > firstLastkey) //firstLastkey = median key (to be removed)
	{
		memcpy(sibling.buffer + 8, buffer + splitIndex + 8, maxIndex - splitIndex - 8);	//copy right half from split except first key

		sibling.keyCount = getKeyCount() - halfKeyCount - 1;	//sibling's key count

		memcpy(&midKey, buffer + splitIndex, sizeof(int));	//copy median key before removing

		memcpy(sibling.buffer, buffer + splitIndex + 4, sizeof(PageId));	//set sibling PageId before removing

		memset(buffer + splitIndex, 0, maxIndex - splitIndex);
		keyCount = halfKeyCount;

		sibling.insert(key, pid);	//key larger than median; insert into sibling
	}
	if(key < lastFirstkey)	//lastFirstkey = median key (to be removed)
	{
		memcpy(sibling.buffer + 8, buffer + splitIndex, maxIndex - splitIndex);	//copy right half from split

		sibling.keyCount = getKeyCount() - halfKeyCount;	//sibling's key count

		memcpy(&midKey, buffer + splitIndex - 8, sizeof(int));	//copy median key before removing

		memcpy(sibling.buffer, buffer + splitIndex - 4, sizeof(PageId));	//set sibling PageId before removing

		memset(buffer + splitIndex - 8, 0, maxIndex - splitIndex + 8);
		keyCount = halfKeyCount - 1;

		insert(key, pid);	//key smaller than median; insert into buffer
	}
	else	//key = median key (to be removed)
	{
		memcpy(sibling.buffer + 8, buffer + splitIndex, maxIndex - splitIndex);	//copy right half from split

		sibling.keyCount = getKeyCount() - halfKeyCount;	//sibling's key count

		midKey = key;

		memcpy(sibling.buffer, &pid, sizeof(PageId));

		memset(buffer + splitIndex, 0, maxIndex - splitIndex);
		keyCount = halfKeyCount;
	}

	return 0;
}

/*
 * Given the searchKey, find the child-node pointer to follow and
 * output it in pid.
 * @param searchKey[IN] the searchKey that is being looked up.
 * @param pid[OUT] the pointer to the child node to follow.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::locateChildPtr(int searchKey, PageId& pid)
{
	int pairSize = sizeof(int) + sizeof(RecordId);
	int i, curKey;
	char* temp = buffer + 8;	//skip first PageId (8 bytes)

	for(i = 8; i < getKeyCount() * pairSize + 8; i += pairSize)
	{
		memcpy(&curKey, temp, sizeof(int));

		if(curKey > searchKey)
		{
			if (i == 8)
			{
				memcpy(&pid, buffer, sizeof(PageId));	//initial PageId
				return 0;
			}
			memcpy(&pid, temp - 4, sizeof(PageId));	//pid from smaller side of curKey
			return 0;
		}

		temp += pairSize;
	}

	memcpy(&pid, temp - 4, sizeof(PageId));

	return 0;
}

/*
 * Initialize the root node with (pid1, key, pid2).
 * @param pid1[IN] the first PageId to insert
 * @param key[IN] the key that should be inserted between the two PageIds
 * @param pid2[IN] the PageId to insert behind the key
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::initializeRoot(PageId pid1, int key, PageId pid2)
{
	RC check;
	char* temp = buffer;

	memset(buffer, 0, PageFile::PAGE_SIZE);

	memcpy(temp, &pid1, sizeof(PageId));	//copy first PageId

	if( (check = insert(key, pid2)) != 0)	//copy first pair
	{
		return check;
	}

	return 0;
}
