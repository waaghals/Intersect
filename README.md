#Image Rating

Trying to find the best images from a large dataset. 
 
##How

###One on One image rating
Images are rated using the ELO algorithm. Images start with 1200 rating and are rated using a K factor of 32.

###Trending images
Images get awarded point for every action. A view, a win, a loss when it is tagged etc. 
The creates a plot of points over a time period. (example)

				Plot							Average Line
		|								|
		|								|
	P	|                   *		P	|                  .
	o	|       *         *			o	|               .
	i	|             *    *		i	|            .
	n	|    *    *     *			n	|         .
	t	|  *						t	|      .
	s	|*							s	|   .
		| *   *							|.
		+---------------------			+---------------------
				Time							Time
			
The average linear line y=ax+b to the plot is calculated. A is the slope, and log(absulute(b)) is the velosity. 
Multiply the slope and velocity and the image which grows the fastest is trending.

An image might have days in which it doesn't get point, those days are not weighted. This is a feature of the algorithm not a bug.

###Tag relations
A network of tags is created. Tag who appear in the same images are treaded as similar. The similarity is the weight between the two nodes in the network.
Tags which are in a distance X from a tag are then considerd similar to that tag.

###Ranks
Administrators and moderators shouldn't be assigned manualy. The system decides if you are active enough to be a moderator or administrator.
If a users is above a certain rank he becomes autoconfirmed, moderator or administrator.
Ranks are divided by the percentile a user is above. 
(Percentile) Rank

+ (0) Rankless
+ (2) Private
+ (4) Private 2
+ (5) Private First Class
+ (9) Specialist
+ (22) Corporal
+ (34) Sergeant _Autoconfirmed_
+ (45) Second Lieutenant
+ (55) First Lieutenant
+ (64) Captain
+ (72) Major
+ (79) Lieutenant Colonel
+ (85) Colonel _Moderator_
+ (90) Brigadier General
+ (94) Major General
+ (97) Lieutenant General
+ (99) General _Administrator_

This way the ranks are always spreaded equeal amongst the users. 1% Of the users is administrator, 2% is Lieutentant General, 3% is Major General so on and so on.

_Version: 0.9_ 

