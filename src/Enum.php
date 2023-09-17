<?php

enum Algorithm: int
{
	case SHA1 = 19;
	case SHA256 = 31;
	case SHA512 = 63;
}

enum Digits: int
{
	case LENGTH_6 = 6;
	case LENGTH_8 = 8;
}

enum Period: int
{
	case SEC_15 = 15;
	case SEC_30 = 30;
	case SEC_60 = 60;
}
