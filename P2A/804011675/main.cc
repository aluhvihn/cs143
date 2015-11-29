/**
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */
 
#include "Bruinbase.h"
#include "SqlEngine.h"
#include <cstdio>

#include "BTreeIndex.h"
#include "BTreeNode.h"

int main()
{
  // run the SQL engine taking user commands from standard input (console).
  // SqlEngine::run(stdin);

 //  int num_insert = 50;
	// BTreeIndex tree;
	// RecordId rid;

	// IndexCursor cursor;
	// int key;
	
	// tree.open("tablename.index", 'w');
	
	// printf("locate 1: %d\n", tree.locate(1, cursor));
	// for (int i = 2; i < num_insert; i++) {
	// 	tree.readForward(cursor, key, rid);
	// 	printf("Next: %d\n", key);
	// }

	// for (int i = 1; i < num_insert; i++) {
	// 	printf("Locate %d: %d\n", i, tree.locate(i,cursor));
	// }

	// tree.close();

 	int num_insert = 50;
	BTLeafNode leaf;
	RecordId rid;

	int eid;
	for (int i = 10; i > 0; i-=2) {
		leaf.insert(i,rid);
		printf("Inserted %d, key count: %d\n", i, leaf.getKeyCount());
	}

	for (int i = 10; i > 0; i--) {
		if(leaf.locate(i,eid) == 0){
			printf("%d found at eid: %d\n", i, eid);
		}
		else printf("%d not found.", i);
	}
  return 0;
}