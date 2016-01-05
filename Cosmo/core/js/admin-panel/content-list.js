/**************************************************
 *          Content List Controller               *
 *             Search content                     *
 **************************************************/

angular.module('cosmo').controller('contentListCtrl', ['$scope', 'REST', 'Hooks', 'Responsive', function($scope, REST, Hooks, Responsive){

    $scope.search = {};
    $scope.exclude = {};
    $scope.exclude.tags = '!exclude';
    $scope.content = {};
    $scope.content.onlySearch = 'all';

   
    // Search
    $scope.searchBar = function(){
        $scope.search = {};
        switch($scope.content.onlySearch){
            case 'type': // Search only the page type
                $scope.search.type = $scope.content.input;
                break;
            case 'author': // Search only the author
                $scope.search.author = $scope.content.input;
                break;
            case 'tags': // Search only the tags
                $scope.search.tags = $scope.content.input;
                break;

            default: // Search anywhere
                $scope.search = $scope.content.input;
                break;
        }
    };

    // Fetch content
    REST.content.query({}, fetchContentPromise);

    // Update the content after it's called
    function fetchContentPromise(data){
        angular.forEach(data, function(data2){
            data2.featured = Hooks.imageHookNotify(Responsive.resize(data2.featured, 'small'));
        });
        $scope.pages = data;
    }
    
    
    
    
    $scope.$on("networkStatusChangeFromParrent",
    	function (event, msg) {
    	//offline,download some data related to the user
    	//for now,I just download all information about movie,assuming the user just like watching movie
    	//and don't like listen to the music         
    		if(msg == true){         //online
    			//let the scope.pages from server
        		REST.content.query({}, fetchContentPromise);
        		
        		function fetchContentPromise(data){
        	        angular.forEach(data, function(data2){
        	            data2.featured = Hooks.imageHookNotify(Responsive.resize(data2.featured, 'small'));
        	        });
        	        $scope.pages = data;
        	    }
    		}else if(msg == false){     //offline
    			var db = openDatabase('localDB', '1.0', 'Local DB', 20 * 1024 * 1024);    //the size of database 20MB may be larger?
        		
                
                db.transaction(function (context) {
                	context.executeSql('CREATE TABLE IF NOT EXISTS content (id unique, title TEXT, description TEXT, header TEXT, subheader TEXT, featured TEXT, body TEXT, url TEXT, type TEXT, published TEXT, published_date int, author int, timestamp int)');
                });
                
                db.transaction(function (context) {
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (6, 'The Peanuts Movie', 'Movie:Snoopy embarks upon his greatest mission as he and his team take to the skies to pursue their arch-nemesis, while his best pal Charlie Brown begins his own epic quest back home to win the love of his life.', 'The Peanuts Movie', '', 'uploads/snoopy-5673be8e572a0.jpg', 'Snoopy embarks upon his greatest mission as he and his team take to the skies to pursue their arch-nemesis, while his best pal Charlie Brown begins his own epic quest back home to win the love of his life.', '/the-peanuts-movie', 'post.html', 'Y', '1450425445', '1', '1450425478')");  
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (7, 'The Lobster', 'Movie:In a dystopian near future, single people, according to the laws of The City, are taken to The Hotel, where they are obliged to find a romantic partner in forty-five days or are transformed into beasts and sent off into The Woods.', 'The Lobster', '', 'uploads/lobster-5673c06510292.jpg', '<span class=\"ng-scope\">ContentIn a dystopian near future, single people, according to the laws of The City, are taken to The Hotel, where they are obliged to find a romantic partner in forty-five days or are transformed into beasts and sent off into The Woods.</span>', '/the-lobster', 'post.html', 'Y', '1450426600', '1', '1450532781')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (8, 'The Walk', 'Movie:2015,Director: Robert Zemeckis', 'The Walk', 'Robert Zemeckis', 'uploads/thewalk-5673cc2ca6f7b.jpg', 'In 1974, high-wire artist Philippe Petit recruits a team of people to help him realize his dream: to walk the immense void between the World Trade Center towers.', '/the-walk', NULL, 'N', '12181626', '1', NULL)");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (9, 'Hotel Transylvania 2', 'Movie:Dracula and his friends try to bring out the monster in his half human, half vampire grandson in order to keep Mavis from leaving the hotel.', 'Hotel Transylvania 2', '', 'uploads/hoteltransylvania-5689e05b3164a.jpg', '<span class=\"ng-scope\">ContentDracula and his friends try to bring out the monster in his half human, half vampire grandson in order to keep Mavis from leaving the hotel.</span>', '/hotel-transylvania-2', 'post.html', 'Y', '1451876304', '1', '1451876304')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (10, 'Scouts Guide to the Zombie Apocalypse', 'Movie:Three scouts, on the eve of their last camp-out, discover the true meaning of friendship when they attempt to save their town from a zombie outbreak.', 'Scouts Guide to the Zombie Apocalypse', '', 'uploads/Scouts-5676a9637ae45.jpg', '<span class=\"ng-scope\">ContentThree scouts, on the eve of their last camp-out, discover the true meaning of friendship when they attempt to save their town from a zombie outbreak.</span>', '/scouts-guide-to-the-zombie-apocalypse', 'post.html', 'Y', '1450532902', '1', '1450532902')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (11, 'Room', 'Movie:After five-year-old Jack and his mother escape from the enclosed surroundings that Jack has known his entire life, the boy makes a thrilling discovery: the outside world.', 'Room ', '', 'uploads/room-5676a93707094.jpg', '<span class=\"ng-scope\">ContentAfter five-year-old Jack and his mother escape from the enclosed surroundings that Jack has known his entire life, the boy makes a thrilling discovery: the outside world.</span>', '/room', 'post.html', 'Y', '1450533068', '1', '1450533068')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (12, 'The Martian', 'Movie:During a manned mission to Mars, Astronaut Mark Watney is presumed dead after a fierce storm and left behind by his crew. But Watney has survived and finds himself stranded and alone on the hostile planet. With only meager supplies, he must draw upon his ', 'The Martian', '', 'uploads/themartian-5676a9886a276.jpg', '<span class=\"ng-scope\">ContentDuring a manned mission to Mars, Astronaut Mark Watney is presumed dead after a fierce storm and left behind by his crew. But Watney has survived and finds himself stranded and alone on the hostile planet. With only meager supplies, he must draw upon his ingenuity, wit and spirit to subsist and find a way to signal to Earth that he is alive.</span>', '/the-martian', 'post.html', 'Y', '1450533147', '1', '1450533147')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (13, 'Minority Report', 'Movie:In a future where a special police unit is able to arrest murderers before they commit their crimes, an officer from that unit is himself accused of a future murder.', 'Minority Report', '', 'uploads/minor-5676a92b3dc1a.PNG', '<span class=\"ng-scope\">ContentIn a future where a special police unit is able to arrest murderers before they commit their crimes, an officer from that unit is himself accused of a future murder.</span>', '/minority-report', 'post.html', 'Y', '1450533259', '1', '1450533259')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (14, 'The Girl with the Dragon Tattoo', 'Movie:Journalist Mikael Blomkvist is aided in his search for a woman who has been missing for forty years by Lisbeth Salander, a young computer hacker.', 'The Girl with the Dragon Tattoo ', '', 'uploads/girldragontattoo-5676a917c87bb.jpg', '<span class=\"ng-scope\">ContentJournalist Mikael Blomkvist is aided in his search for a woman who has been missing for forty years by Lisbeth Salander, a young computer hacker.</span>', '/the-girl-with-the-dragon-tattoo', 'post.html', 'Y', '1450533345', '1', '1450533345')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (15, 'The Hateful Eight', 'Movie:In post-Civil War Wyoming, bounty hunters try to find shelter during a blizzard but get involved in a plot of betrayal and deception. Will they survive?', 'The Hateful Eight', '', 'uploads/thehateful8-5676a9808819a.jpg', '<span class=\"ng-scope\">ContentIn post-Civil War Wyoming, bounty hunters try to find shelter during a blizzard but get involved in a plot of betrayal and deception. Will they survive?</span>', '/the-hateful-eight', 'post.html', 'Y', '1450615204', '1', '1450615204')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (16, 'Star Wars: Episode VI - Return of the Jedi', 'Movie:After rescuing Han Solo from the palace of Jabba the Hutt, the rebels attempt to destroy the second Death Star, while Luke struggles to make Vader return from the dark side of the Force.', 'Star Wars: Episode VI - Return of the Jedi', '', 'uploads/Star_Wars_Episode_VI__Return_of_the_Jedi_da3cec78-5676a971e9962.jpg', '<span class=\"ng-scope\">ContentAfter rescuing Han Solo from the palace of Jabba the Hutt, the rebels attempt to destroy the second Death Star, while Luke struggles to make Vader return from the dark side of the Force.</span>', '/star-wars-episode-vi---return-of-the-jedi', 'post.html', 'Y', '1450615387', '1', '1450615387')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (17, 'Birdman or (The Unexpected Virtue of Ignorance)', 'Movie:Illustrated upon the progress of his latest Broadway play, a former popular actor''s struggle to cope with his current life as a wasted actor is shown.', 'Birdman or (The Unexpected Virtue of Ignorance)', '', 'uploads/birdman-5676a7102ccd7.jpg', '<span class=\"ng-scope\">ContentIllustrated upon the progress of his latest Broadway play, a former popular actor''s struggle to cope with his current life as a wasted actor is shown.</span>', '/birdman-or-the-unexpected-virtue-of-ignorance', 'post.html', 'Y', '1450615485', '1', '1450615485')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (18, 'You Can''t Take It With You', 'Movie:A man from a family of rich snobs becomes engaged to a woman from a good-natured but decidedly eccentric family.', 'You Can''t Take It With You', '', 'uploads/You_Can''t_Take_It_with_You_(1938)-5676a99d48f7a.jpg', '<span class=\"ng-scope\">ContentA man from a family of rich snobs becomes engaged to a woman from a good-natured but decidedly eccentric family.</span>', '/you-cant-take-it-with-you', 'post.html', 'Y', '1450615563', '1', '1450615563')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (19, 'Stranger Than Paradise', 'Movie:A self-styled New York hipster is paid a surprise visit by his younger cousin from Budapest. From initial hostility and indifference a small degree of affection grows between the two. Along with a friend, they eventually end up visiting their aunt in the ', 'Stranger Than Paradise', '', 'uploads/strangerthanparadise-5676a978901bc.jpg', '<span class=\"ng-scope\">ContentA self-styled New York hipster is paid a surprise visit by his younger cousin from Budapest. From initial hostility and indifference a small degree of affection grows between the two. Along with a friend, they eventually end up visiting their aunt in the wastelands of Cleveland and then proceed to Florida where they lose all their money gambling before unwittingly gaining a fortune.</span>', '/stranger-than-paradise', 'post.html', 'Y', '1450615701', '1', '1450615701')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (20, 'Christmas, Again', 'Movie:A heartbroken Christmas-tree salesman returns to New York City hoping to put his past behind him. Living in a trailer and working the night shift, he begins to spiral downwards until the saving of a mysterious woman and some colorful customers rescue him ', 'Christmas, Again', '', 'uploads/christmasagain-5676a8d551cfa.jpg', '<span class=\"ng-scope\">ContentA heartbroken Christmas-tree salesman returns to New York City hoping to put his past behind him. Living in a trailer and working the night shift, he begins to spiral downwards until the saving of a mysterious woman and some colorful customers rescue him from self-destruction.</span>', '/christmas-again', 'post.html', 'Y', '1450615772', '1', '1450615772')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (21, 'Feast', 'Movie:The love life of a man as told through the meals he gives his adopted dog, Winston.', 'Feast', '', 'uploads/feast-5676a8f7583c7.jpg', '<span class=\"ng-scope\">ContentThe love life of a man as told through the meals he gives his adopted dog, Winston.</span>', '/feast', 'post.html', 'Y', '1450615880', '1', '1450615880')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (22, 'Ice Age', 'Movie:Set during the Ice Age, a sabertooth tiger, a sloth, and a wooly mammoth find a lost human infant, and they try to return him to his tribe.', 'Ice Age', '', 'uploads/iceage-5676a9202c8ae.jpg', '<span class=\"ng-scope\">ContentSet during the Ice Age, a sabertooth tiger, a sloth, and a wooly mammoth find a lost human infant, and they try to return him to his tribe.</span>', '/ice-age', 'post.html', 'Y', '1450615981', '1', '1450615981')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (23, 'Bone Tomahawk', 'Movie:Four men set out in the Wild West to rescue a group of captives from cannibalistic cave dwellers.', 'Bone Tomahawk ', '', 'uploads/Bone_Tomahawk-5676a6d3401fd.jpg', '<span class=\"ng-scope\">ContentFour men set out in the Wild West to rescue a group of captives from cannibalistic cave dwellers.</span>', '/bone-tomahawk', 'post.html', 'Y', '1450616222', '1', '1450616222')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (24, 'Get a Horse!', 'Movie:Mickey, Minnie, Horace Horsecollar, and Clarabelle Cow go on a musical wagon ride until Peg-Leg Pete tries to run them off the road.', 'Get a Horse!', '', 'uploads/getahorserun-5676a902c9a6c.jpg', '<span class=\"ng-scope\">ContentMickey, Minnie, Horace Horsecollar, and Clarabelle Cow go on a musical wagon ride until Peg-Leg Pete tries to run them off the road.</span>', '/get-a-horse', 'post.html', 'Y', '1450616412', '1', '1450616412')");
                	context.executeSql("INSERT INTO content (id, title, description, header, subheader, featured, body, url, type, published, published_date, author, timestamp) VALUES (25, 'The Face of an Angel', 'Movie:Both a journalist and a documentary filmmaker chase the story of a murder and its prime suspect.', 'The Face of an Angel', '', 'uploads/faceofangel-5676a8e8cd7cb.jpg', '<span class=\"ng-scope\">ContentBoth a journalist and a documentary filmmaker chase the story of a murder and its prime suspect.</span>', '/the-face-of-an-angel', 'post.html', 'Y', '1450616470', '1', '1450616470')");
                });
                
                db.transaction(function (context) {
                	context.executeSql("select * from content", [], function (context, results) {
                		var len = results.rows.length, i;
                		var array = new Array();
                		for(i = 0; i < len; i++){
                			array[i] = results.rows.item(i);
                		}
                		
                		$scope.pages = array;
                	}, null);
                	
                });
                
    		}
    	});
    
    
    
}]);
