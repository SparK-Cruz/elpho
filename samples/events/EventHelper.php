<?php
	import(php.event.Event);
	
	class EventHelper extends Event{
		public function getName(){
			return "EVENT_HELPER";
		}
	}
