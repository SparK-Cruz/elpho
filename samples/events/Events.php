<?php
	require("../../startup.php");
	
	class Events{
		public static final function main($args=array()){
			$dispatcher = new DispatcherHelper();
			$hardListener = new HardListener();
			$dynamicListener = function($event){
				print("<pre>");
				print("DynamicListener ".get_class($event)."'s target is ".$event->getTarget());
				print("</pre>");
			};
			
			$dispatcher->addEventListener(EventHelper, array($hardListener,'listeningMethod'));
			$dispatcher->addEventListener(EventHelper, $dynamicListener);
			
			$dispatcher->run();
		}
	}
