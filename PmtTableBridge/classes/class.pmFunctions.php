<?php
/**
 * class.PmtTableBridge.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// PmtTableBridge PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

function PmtTableBridge_getMyCurrentDate()
{
	return G::CurDate('Y-m-d');
}

function PmtTableBridge_getMyCurrentTime()
{
	return G::CurDate('H:i:s');
}
