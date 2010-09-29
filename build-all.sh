#!/bin/bash

APPDIR=`dirname $0`;
PHPMD="$APPDIR/phpmd.txt"
PHPCS="$APPDIR/phpcs.txt"

phing build distr \
	-Dphpunit.format=plain -Dphpunit.save=no \
	-Dphpmd.format=text -Dphpmd.output=$PHPMD \
	-Dphpcs.format=full -Dphpcs.output=$PHPCS \
	-Ddocs=yes -Ddocs.converter=HTML:frames:default_utf
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
