cd ..

@echo Update revision
svn info | findstr "Date Revision" > Revision

@echo Create readme.txt
copy /Y docs\Postie.txt+docs\Installation.txt+docs\Usage.txt+docs\FAQ.txt+docs\Changes.txt readme.txt

@echo Create readme.html & faq.html
curl -F "text=1" -F "readme_contents=<readme.txt" http://wordpress.org/plugins/about/validator/ | perl deploy\stripParts.pl > readme.html

cd deploy
