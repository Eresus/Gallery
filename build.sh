#!/bin/bash

APPDIR=`dirname $0`;
PHPMD="$APPDIR/phpmd.txt"
PHPCS="$APPDIR/phpcs.txt"

if [ $# -gt 0 ]
then
	BUILDDIR="-Dbuild.dir=$1"
else
	BUILDDIR=""
fi

phing build \
	$BUILDDIR \
	-Dphpunit.format=plain -Dphpunit.save=no \
	-Dphpmd.format=text -Dphpmd.output=$PHPMD \
	-Dphpcs.format=full -Dphpcs.output=$PHPCS
if [ -f $PHPMD ]
then
	cat $PHPMD
	unlink $PHPMD
fi

if [ -f $PHPCS ]
then
	cat $PHPCS
	unlink $PHPCS
fi
