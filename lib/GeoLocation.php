<?php
/**
 * GeoLocation
 *
 * http://stackoverflow.com/questions/10053358/measuring-the-distance-between-two-coordinates-in-php
 *
 * Processes location information to decide which session to join
 */

class GeoLocation
{
	/**
	 * Calculates the great-circle distance between two points, with the Vincenty formula.
	 *
	 * @param float $latitudeFrom Latitude of start point in [deg decimal]
	 * @param float $longitudeFrom Longitude of start point in [deg decimal]
	 * @param float $latitudeTo Latitude of target point in [deg decimal]
	 * @param float $longitudeTo Longitude of target point in [deg decimal]
	 * @param float $earthRadius Mean earth radius in [m]
	 * @return float Distance between points in [m] (same as earthRadius)
	 */
	public static function vincentyGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
	{
		$latFrom = deg2rad($latitudeFrom);
		$lonFrom = deg2rad($longitudeFrom);
		$latTo = deg2rad($latitudeTo);
		$lonTo = deg2rad($longitudeTo);
		$lonDelta = $lonTo - $lonFrom;
		$a = pow(cos($latTo) * sin($lonDelta), 2) +pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
		$b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
		$angle = atan2(sqrt($a), $b);
		return $angle * $earthRadius;
	}

	/**
	 * Checks for locations matching a users location
	 *
	 * @param string $coordinates Users coordinates
	 * @param array $sessions List of sessions
	 * @param int $radius Radius to match within
	 * @return array Matched locations
	 */
	public static function getLocationMatch($coordinates, $sessions, $radius = 150)
	{
		$coordinates = GeoLocation::parseCoordinates($coordinates);
		foreach($sessions as $session)
		{
			$session_coordinates = GeoLocation::parseCoordinates($session['coordinates']);
			if(GeoLocation::vincentyGreatCircleDistance($coordinates['latitude'], $coordinates['longitude'], $session_coordinates['latitude'], $session_coordinates['longitude']) <= $radius)
			{
				return $session['id'];
			}
		}
		return false;
	}

	/**
	 * Parse coordinates apart
	 *
	 * @param string $coordinates
	 * @return array Coordinates
	 */
	public static function parseCoordinates($coordinates)
	{
		$coordinates = "123.456789,-987.654321";
		$coordinates = explode(",", $coordinates);
		$coordinates = array(
			'latitude' => $coordinates[0],
			'longitude' => $coordinates[1],
		);
		return $coordinates;
	}
}
?>