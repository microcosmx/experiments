/**************************************************
 *          Online Offline Controller               *
 *                                                  *
 **************************************************/

angular.module('cosmo').controller('onlineCtrl', ['$scope', 'REST', 'Hooks', 'Responsive','$http', '$interval', function($scope, REST, Hooks, Responsive, $http, $interval){

    $scope.search = {};
    $scope.exclude = {};
    $scope.exclude.tags = '!exclude';
    $scope.content = {};
    $scope.content.onlySearch = 'all';
    $scope.button = "Online";

    $interval(callAtInterval, 1000);

    /*
    function callAtInterval() {
    	$scope.content.online = $http({ method: 'HEAD', url: '/' + window.location.hostname + "/?rand=" + Math.floor((1 + Math.random()) * 0x10000) })
        .then(function(response) {
          var status = response.status;
          if(status >= 200 && status < 300 || status === 304){
        	  return true;
          }else{
        	  return false;
          }
          
        });
    	
    	if($scope.content.online){      //set the button
    		$scope.button = "Online ";
    		$scope.$emit("networkStatusChange", true);
    	}else{
    		$scope.button = "Offline";
    		$scope.$emit("networkStatusChange", false);
    	}
    }*/
    
    
    function callAtInterval() {
    	$http({ method: 'HEAD', url: '/' + window.location.hostname + "/?rand=" + Math.floor((1 + Math.random()) * 0x10000) })
    	.success(function (data, status, headers, config) {
    			$scope.button = "Online ";
    			$scope.$emit("networkStatusChange", true);
    		}).
    	error(function (data, status, headers, config) {
    			$scope.button = "Offline";
        		$scope.$emit("networkStatusChange", false);
    		});
    }
    
    
    //toggle the internet status between online and offline
    $scope.toggle = function(){
    	$scope.content.online = !$scope.content.online;
    	
    	if($scope.content.online){      //set the button
    		$scope.button = "Online ";
    		$scope.$emit("networkStatusChange", true);
    	}else{
    		$scope.button = "Offline";
    		$scope.$emit("networkStatusChange", false);
    	}
    	
    }

}]);
