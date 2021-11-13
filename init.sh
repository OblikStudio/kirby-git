#!/bin/bash

declare -r origin=content-origin
declare -r clone=content

rm -rf $origin
rm -rf $clone

mkdir -p $origin
cd $origin
git init
git config receive.denyCurrentBranch updateInstead

mkdir -p home
echo "Title: Home" > home/home.txt 
echo "Title: Kirby Git" > site.txt 
git add .
git commit -m "initial commit"

cd ..
git clone $origin $clone

echo -e "\n\033[1;32mSuccess!\033[0m"
echo -e "You can now use make changes to '$clone' via the panel and push them" \
"to '$origin' with kirby-git. Then, you can make changes to '$origin' with" \
"your text editor, commit them, and pull them in '$clone' via kirby-git."
