rem this script updates the version number in all relevant files in the directory
if [ $# -ne 1 ] 
  echo updating revision but not versionNum
  GOTO REVISION
else
  VER=$1
  for file in *.{php,js,txt,css}; do
    perl -pe "s/(version|stable tag): [0-9a-zA-Z\.]+( |$)/\$1: $VER/gi" < $file > ${file}TEMP
  done
  for file in *TEMP; do
    mv -f $file `echo $file|perl -pe 's/TEMP//'`
  done
fi

:REVISION
# update Revision file
svn up
svn info | grep -E '(Date|Revision)' > Revision
# convert readme.txt to readme.html using wordpress's online converter
wget --post-data='url=1&readme_url=robfelty.com/wptesting/wp-content/plugins/postie/readme.txt' http://wordpress.org/extend/plugins/about/validator/ -O - |./stripParts.pl > readme.html
#svn commit
