/*
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */
 
#include "BTreeIndex.h"
#include "BTreeNode.h"

using namespace std;

/*
 * BTreeIndex constructor
 */
BTreeIndex::BTreeIndex()
{
    rootPid = -1;
    treeHeight = 0;
}

/*
 * Open the index file in read or write mode.
 * Under 'w' mode, the index file should be created if it does not exist.
 * @param indexname[IN] the name of the index file
 * @param mode[IN] 'r' for read, 'w' for write
 * @return error code. 0 if no error
 */
RC BTreeIndex::open(const string& indexname, char mode)
{
	RC check = pf.open(indexname, mode);

	if(check < 0) 
	{
		return check;
	}

	rootPid = 0;

	if(pf.endPid() <= 0) 	//index not initialized
	{
		BTLeafNode root;
		return root.write(rootPid, pf);
	}

	return 0;
}

/*
 * Close the index file.
 * @return error code. 0 if no error
 */
RC BTreeIndex::close()
{
    rootPid = -1;
    return pf.close();
}

/*
 * Insert (key, RecordId) pair to the index.
 * @param key[IN] the key for the value inserted into the index
 * @param rid[IN] the RecordId for the record being inserted into the index
 * @return error code. 0 if no error
 */
RC BTreeIndex::insert(int key, const RecordId& rid)
{
	RC rc;
	// B Tree Empty
	if (rootPid==-1) {
		// Insert pair as root node
		BTLeafNode rootNode;
		rootPid = pf.endPid();
		rc = rootNode.write(rootPid, pf);
		rootNode.insert(key,rid);
		treeHeight = 1;
	}
	// Find out where to insert new key & record
	else {
		// Return values (destination) for inserting
		PageId ret_pid;
		int ret_key;
		// Insert helper function; start from height 1
		rc = insert_key(rid, key, rootPid, ret_key, ret_pid, 1);
	}
  return rc;
}

RC BTreeIndex::insert_key( const RecordId& rid, int key, PageId start_pid,  int& ret_key, PageId ret_pid, int curr_height)
{
	RC rc;
	// If the pid is a non-leaf node
	if (curr_height != treeHeight) {
		BTNonLeafNode nleafnode;
		PageId next_pid, split_pid;
		int split_key
		
		// Read node from pagefile
		nleafnode.read(start_pid, pf);
		nleafnode.locateChildPtr(key, next_pid);	// Determine where to go in the tree
		
		// Recursively use function until we reach a leaf node
		// Try to insert starting from next_pid (next page id), which is in curr_height+1.
		// Return values for pid and key in split_pid and split_key
		rc = this->insert_key(key, rid, next_pid, curr_height+1, split_pid, split_key)
		if (rc == RC_NODE_FULL) {
			rc = nleafnode.insert(split_key, split_pid);
			if (!rc) {	// Successfully insert
				nleafnode.write(start_pid, pf);
				return rc;
			}
			// Unsuccessful insert; node is full
			rc = RC_NODE_FULL;
			// Split node
			int s_key;
			BTNonLeafNode split_node;
			nleafnode.insertAndSplit(split_key, split_pid, split_node, s_key);

			PageId split_pid = pf.endPid();
			//  Write the nodes
			split_node.write(split_pid, pf);
			nleafnode.write(start_pid, pf);
			
			ret_pid = split_pid;
			ret_key = s_key;
			treeHeight++;
			return rc;
		}
		return rc;
	}
	// pid is a leaf node
	else {
		BTLeafNode leafnode;
		leafnode.read(start_pid, pf);

		rc = leafnode.insert(key, rid);
		if (!rc) {	// Successfully insert
			leafnode.write(start_pid, pf);
			return rc;
		}
		// Unsuccessful insert; node is full
		int s_key;
		BTLeafNode split_node;
		// Split the node
		leafnode.insertAndSplit(key, rid, split_node, s_key);

		PageId split_pid = pf.endPid();
		leafnode.setNextNodePtr(split_pid);
		// Write the nodes
		split_node.write(split_pid, pf);
		leafnode.write(start_pid, pf);

		ret_pid = split_pid;
		ret_key = s_key;
		treeHeight++;
		return rc;
	}
}

/**
 * Run the standard B+Tree key search algorithm and identify the
 * leaf node where searchKey may exist. If an index entry with
 * searchKey exists in the leaf node, set IndexCursor to its location
 * (i.e., IndexCursor.pid = PageId of the leaf node, and
 * IndexCursor.eid = the searchKey index entry number.) and return 0.
 * If not, set IndexCursor.pid = PageId of the leaf node and
 * IndexCursor.eid = the index entry immediately after the largest
 * index key that is smaller than searchKey, and return the error
 * code RC_NO_SUCH_RECORD.
 * Using the returned "IndexCursor", you will have to call readForward()
 * to retrieve the actual (key, rid) pair from the index.
 * @param key[IN] the key to find
 * @param cursor[OUT] the cursor pointing to the index entry with
 *                    searchKey or immediately behind the largest key
 *                    smaller than searchKey.
 * @return 0 if searchKey is found. Othewise an error code
 */
RC BTreeIndex::locate(int searchKey, IndexCursor& cursor)
{
	BTLeafNode tempLeaf;
	BTNonLeafNode tempNonLeaf;
	
	cursor.pid = rootPid;
	int currHeight = 1;

	RC check;
	
	while(currHeight != treeHeight)
	{
		check = tempNonLeaf.read(cursor.pid, pf);
		
		if(check < 0)	//check load errors
		{
			return check;
		}
		
		//locate child node and update cursor.pid
		check = tempNonLeaf.locateChildPtr(searchKey, cursor.pid);
		
		if(check < 0)	//check errors 
		{
			return check;
		}
		
		currHeight++;
	}
	
	check = tempLeaf.read(cursor.pid, pf);
		
	if(check < 0)	//check load errors
	{
		return check;
	}
	
	//locate leaf node and update cursor.eid
	check = tempLeaf.locate(searchKey, cursor.eid);
	
	if(check < 0)	//check errors 
	{
		return check;
	}
	
	return 0;
}

/*
 * Read the (key, rid) pair at the location specified by the index cursor,
 * and move foward the cursor to the next entry.
 * @param cursor[IN/OUT] the cursor pointing to an leaf-node index entry in the b+tree
 * @param key[OUT] the key stored at the index cursor location.
 * @param rid[OUT] the RecordId stored at the index cursor location.
 * @return error code. 0 if no error
 */
RC BTreeIndex::readForward(IndexCursor& cursor, int& key, RecordId& rid)
{
	RC rc = 0;
	BTLeafNode tempLeaf;
	RC check;

	while(true)
	{
		check = tempLeaf.read(cursor.pid, pf);

		if(check < 0)	//check load errors
		{
			return check;
		}

		check = tempLeaf.readEntry(cursor.eid, key, rid);	//read the pair from cursor.eid

		if(check < 0)	//check errors
		{
			return check;
		}
		
		if(check == 0) 
		{
			cursor.eid++; //increment eid (points to next entry)
			return 0;
		}
		
		cursor.pid = tempLeaf.getNextNodePtr();	//record doesnt exist; get next node
		cursor.eid = 0;

		if(cursor.pid == INVALID_PID)	//no more nodes
		  return RC_END_OF_TREE;
		}
	}

	return rc;
}
