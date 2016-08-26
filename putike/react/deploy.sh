#!/bin/bash
# 当前目录需要在react/下
svn up
npm run clean 
svn rm src/static/*.js
svn rm src/static/*.css
svn rm src/static/index.html

npm run deploy:prod

svn add dist/*

svn ci -m 'new version ' dist/
rm -rf src/static
svn up src/static
npm run clean 

rm *.gzip
