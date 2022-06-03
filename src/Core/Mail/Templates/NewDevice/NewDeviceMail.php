<?php
	namespace Core\Mail\Templates\NewDevice;

	class NewDeviceMail
	{
		public static ?string $date;
		public static ?string $browser;
		public static ?string $platform;
		public static ?string $ipAddress;
		public static string $locale = EZENV["DEFAULT_LOCALE"];
	}
?>