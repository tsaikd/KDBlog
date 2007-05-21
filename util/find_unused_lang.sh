#!/bin/bash

cd "$(dirname "${BASH_SOURCE}")/.."

phplist="$(find -maxdepth 2 -type f \
	! -wholename "*/.svn/*" \
	! -wholename "*/lang/*" \
	-iwholename "*.php"
)"

for langfile in ./lang/* ; do
	while read fbuf ; do
		[ -z "$(grep '$BLOGLANG' <<<"${fbuf}")" ] && continue

		langbuf="$(cut -d' ' -f1 <<<"${fbuf}")"
		grepexp="$(echo "${langbuf}" | sed 's/\[/\\\[/g' | sed 's/\]/\\\]/g')"

		if [ -z "$(grep "${grepexp}" ${phplist})" ] ; then
			echo "${langbuf} is never used in '${langfile}'"
		fi
	done < "${langfile}"
done

