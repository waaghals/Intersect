#Image Rating

Trying to find the best images from a large dataset. 
 
##What makes a good image

* Larger images are considered better 
* Images with less noise are better 
* EXIF data is extracted for GEO info. Images takes locally are considered better 
* Images which look the same should have a rating more or less the same 
* Images who share are a lot of common tags are more relevant to each other and should have a a rating more or less the same 
* Links between images are made based on their corresponding tags, rating, size, location and if the look the same. 
 
##Rating algorithm

It is harder for people to rate single things from 1 to 10 then to simply pick a winner from two things. 
For that reason I use a ELO derived rating system as the base for the rating. 
The ELO rating system is used for chess and go. It rates two players against each other based on the outcome of the match and the expected ratings after the match. 
 
###Similarities with ELO

Each images gets a base rating of 1200 point. Images take on 'each other' in a image vs. image rating with a Kfactor of 32. 
However the images get extra points for the image size which is taken into account when the expected ratings are calculated. 
 
##Tagging system

I going to implant a tagging system based. To find similar tags between images and their 'weight' a graph approach will be used.