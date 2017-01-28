<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Détection de l'agent utilisateur
	 * 
	 * Cette classe permet de détecter différentes caractéristiques du client à partir de son agent utilisateur. Entre autre on peut détecter le navigateur ou s'il s'agit d'un crawler.
	 * 
	 * @package    framework
	 * @subpackage classes/tools
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    11/11/2015
	 * @since      20/06/2015
	 * @link       http://udger.com
	 */
	final class AgentDetector {
		/*
		 * DONNÉES:
		 * Les plate-formes, les périphériques, et les navigateurs proviennent de la version « 20150104-01 » du fichier XML du site « http://udger.com ».
		 * Les crawlers proviennent de la version « 20140812-01 » du fichier XML du site « http://user-agent-string.info », désormais « http://udger.com ».
		 */
		/*
		 * AJOUTS PERSONNALISÉS: (En attente d'une version plus récente gratuite sur « udger.com », disponible aux membres enregistrés)
		 * 11/11/2015: Ajout de « Android marshmallow 6.0 »
		 * 30/07/2015: Ajout de « Microsoft Edge » et de « Android lollipop 5.1 »
		 * 20/06/2015: Ajout de « sp_auditbot » et de « Android lollipop 5.0 »
		 */
		/*
		 * CHANGELOG:
		 * 21/06/2015: Correction d'un bug se produisant avec les « foreach » imbriqués (« break; » désormais remplacé par « break 2; » pour quitter les deux boucles au lieu d'une seule)
		 * 20/06/2015: Version initiale
		 */

		/**
		 * Nombre de caractères maximum d'un type
		 */
		const TYPE_MAX_SIZE = 20;

		/**
		 * Nombre de caractères maximum d'un périphérique
		 */
		const DEVICE_MAX_SIZE = 17;

		/**
		 * Nombre de caractères maximum d'une plate-forme
		 */
		const PLATFORM_MAX_SIZE = 32;

		/**
		 * Nombre de caractères maximum d'une famille de plate-formes
		 */
		const PLATFORM_FAMILY_MAX_SIZE = 19;

		/**
		 * Nombre de caractères maximum d'un navigateur
		 */
		const BROWSER_MAX_SIZE = 36;

		/**
		 * Nombre de caractères maximum d'un crawler
		 */
		const ROBOT_MAX_SIZE = 40;

		/**
		 * Nombre de caractères maximum d'une famille de crawlers
		 */
		const ROBOT_FAMILY_MAX_SIZE = 40;


		/**
		 * Tableau d'expressions régulières de plate-formes en fonction de leur nom, regroupées par familles
		 * 
		 * @var array
		 */
		private static $platforms = array(
			'Windows' => array(
				'/^Mozilla.*Windows Phone 8.1/si' => 'Windows Phone 8.1',
				'/Win 9x 4\.90/si' => 'Windows ME',
				'/windows nt 6\.2.*ARM/si' => 'Windows RT',
				'/^HTC_HD2.*Opera.*windows/si' => 'Windows Phone 7',
				'/^Mozilla.*MSIE.*Windows NT 6.1.* Xbox/si' => 'Xbox platform',
				'/^Mozilla.*Windows Phone 8.0/si' => 'Windows Phone 8',
				'/^Mozilla.*MSIE.*Windows.* XBLWP7/si' => 'Windows Phone 7',
				'/Windows Phone OS 7/' => 'Windows Phone 7',
				'/(Windows Mobile)|(Windows Phone)/si' => 'Windows Mobile',
				'/windows nt 5\.1/si' => 'Windows XP',
				'/.*windows 95.*/si' => 'Windows 95',
				'/windows nt 5\.0/si' => 'Windows 2000',
				'/.*windows nt 5\.2( |;).*/si' => 'Windows 2003 Server',
				'/.*win95.*/si' => 'Windows 95',
				'/windows 98/si' => 'Windows 98',
				'/.*win16( |;).*/si' => 'Windows 3.x',
				'/.*win98( |;).*/si' => 'Windows 98',
				'/.*windows 4\.10( |;).*/si' => 'Windows 98',
				'/windows ce|PocketPC/si' => 'Windows CE',
				'/.*windows me( |;).*/si' => 'Windows ME',
				'/.*windows nt 6\.0( |;).*/si' => 'Windows Vista',
				'/webtv/si' => 'MSN TV (WebTV)',
				'/winnt/si' => 'Windows NT',
				'/WinNT4\.0/si' => 'Windows NT',
				'/Windows\-NT/si' => 'Windows NT',
				'/CYGWIN_NT\-5.0/si' => 'Windows 2000',
				'/.*windows 3\.1.*/si' => 'Windows 3.x',
				'/windows nt 6\.1/si' => 'Windows 7',
				'/.*windows 2000( |;).*/si' => 'Windows 2000',
				'/Windows NT 6\.0/si' => 'Windows Vista',
				'/Windows_XP\/5.1/si' => 'Windows XP',
				'/.*windows 7.*/si' => 'Windows 7',
				'/windows nt 6\.2/si' => 'Windows 8',
				'/.*Windows\-Vista/si' => 'Windows Vista',
				'/^XBMC.*Xbox.*www\.xbmc\.org/si' => 'Xbox platform',
				'/Windows NT 6\.3/si' => 'Windows 8.1',
				'/Windows NT 10\.0/si' => 'Windows 10', // Ajouté 2015-07-30
				'/Windows 2000/si' => 'Windows 2000',
				'/Windows NT 4/si' => 'Windows NT',
				'/.*windows XP.*/si' => 'Windows XP',
				'/Windows ME/si' => 'Windows ME',
				'/NT4\.0/si' => 'Windows NT',
				'/windows/si' => 'Windows',
				'/Win32/si' => 'Windows',
			),
			'Palm OS' => array(
				'/palm/si' => 'Palm OS',
			),
			'MorphOS' => array(
				'/MorphOS/si' => 'MorphOS',
			),
			'iOS' => array(
				'/iPhone OS 5_[0-9_]+/si' => 'iOS 5',
				'/iPhone OS 4_[0-9_]+/si' => 'iOS 4',
				'/iPad.*OS 5_[0-9_]+/si' => 'iOS 5',
				'/iPad.*OS 6_[0-9_]+/si' => 'iOS 6',
				'/iPhone OS 6_[0-9_]+/si' => 'iOS 6',
				'/iPhone.*OS 7[0-9_\.]+/si' => 'iOS 7',
				'/iPad.*OS 7[0-9_\.]+/si' => 'iOS 7',
				'/iPad.*OS 8[0-9_\.]+/si' => 'iOS 8',
				'/iPhone OS 8[0-9_\.]+/si' => 'iOS 8',
				'/iPhone OS 3_[0-9_]+/si' => 'iOS 3',
				'/iPhone OS ([0-9_]+) like Mac OS X/si' => 'iOS',
				'/iPhone OS 2_0/si' => 'iOS',
				'/iPhone.*like Mac OS X/si' => 'iOS',
				'/iPad.*OS.*like Mac OS X/si' => 'iOS',
				'/iPhone OS [0-9\.]+/si' => 'iOS',
				'/^Mozilla.*Linux.*AppleWebKit.*Puffin\/[0-9\.]+IT|IP$/si' => 'iOS',
				'/.*\/.*CFNetwork\/(602|609|609\.1\.4) Darwin\//si' => 'iOS 6',
				'/.*\/.*CFNetwork\/(672\.0\.2|672\.0\.8|672\.1\.12|672\.1\.13|672\.1\.14|672\.1\.15) Darwin\//si' => 'iOS 7',
				'/.*\/.*CFNetwork\/(485\.2|485\.10\.2|485\.12\.7|485\.12\.30|485\.13\.9) Darwin\//si' => 'iOS 4',
				'/.*\/.*CFNetwork\/(548\.0\.3|548\.0\.4|548\.1\.4) Darwin\//si' => 'iOS 5',
				'/.*\/.*CFNetwork\/459 Darwin\//si' => 'iOS 3',
				'/iPhone/si' => 'iOS',
			),
			'Solaris' => array(
				'/Solaris/si' => 'Solaris',
				'/sunos/si' => 'Solaris',
			),
			'Symbian OS' => array(
				'/Series60/si' => 'Symbian OS',
				'/Series80\/2\.0/si' => 'Symbian OS',
				'/SonyEricssonP900/si' => 'Symbian OS',
				'/Series90.*Nokia7710/si' => 'Symbian OS',
				'/^DoCoMo.*F900i/si' => 'Symbian OS',
				'/SymbianOS/si' => 'Symbian OS',
				'/S60; SymbOS/si' => 'Symbian OS',
				'/symbian/si' => 'Symbian OS',
				'/NokiaN70/si' => 'Symbian OS',
				'/Series 60/si' => 'Symbian OS',
				'/NokiaN97/si' => 'Symbian OS',
				'/Nokia.*XpressMusic/si' => 'Symbian OS',
				'/NokiaE66/si' => 'Symbian OS',
				'/Nokia6700/si' => 'Symbian OS',
			),
			'Linux' => array(
				'/^Mozilla\/.*Ubuntu.*[Tablet|Mobile].*WebKit/si' => 'Ubuntu Touch',
				'/centos/si' => 'Linux (CentOS)',
				'/ubuntu/si' => 'Linux (Ubuntu)',
				'/linux.*debian/si' => 'Linux (Debian)',
				'/linux.*fedora/si' => 'Linux (Fedora)',
				'/linux.*gentoo/si' => 'Linux (Gentoo)',
				'/linux.*linspire/si' => 'Linux (Linspire)',
				'/linux.*mandriva/si' => 'Linux (Mandriva)',
				'/linux.*mdk/si' => 'Linux (Mandriva)',
				'/linux.*redhat/si' => 'Linux (RedHat)',
				'/linux.*slackware/si' => 'Linux (Slackware)',
				'/linux.*kanotix/si' => 'Linux (Kanotix)',
				'/linux.*suse/si' => 'Linux (SUSE)',
				'/linux.*knoppix/si' => 'Linux (Knoppix)',
				'/linux.*\(Dropline GNOME\).*/si' => 'Linux (Slackware)',
				'/linux.*red hat/si' => 'Linux (RedHat)',
				'/Red Hat modified/si' => 'Linux (RedHat)',
				'/Vector Linux/si' => 'Linux (VectorLinux)',
				'/Linux Mint/si' => 'Linux (Mint)',
				'/suse\-linux/si' => 'Linux (SUSE)',
				'/Arch Linux ([0-9a-zA-Z\.\-]+)/si' => 'Linux (Arch Linux)',
				'/Mozilla.*Linux.*Maemo/si' => 'Linux (Maemo)',
				'/PCLinuxOS\/([0-9a-z\.\-]+)/si' => 'PClinuxOS',
				'/^Mozilla\/.*Linux.*Jolicloud/si' => 'Joli OS',
				'/^Mozilla.*CrOS.*Chrome/si' => 'Chrome OS',
				'/Linux.*Mageia/si' => 'Linux (Mageia)',
				'/Samsung.*SmartTV/si' => 'Linux',
				'/VectorLinux/si' => 'Linux (VectorLinux)',
				'/Mageia.*Linux/si' => 'Linux (Mageia)',
				'/linux/si' => 'Linux',
				'/Gentoo i686/si' => 'Linux (Gentoo)',
				'/Konqueror.*SUSE/si' => 'Linux (SUSE)',
				'/Konqueror.*Fedora/si' => 'Linux (Fedora)',
				'/\(GNU;/si' => 'GNU OS',
				'/Unix/si' => 'Linux',
			),
			'Android' => array(
				'/Android 4\.1/si' => 'Android 4.1.x Jelly Bean',
				'/Android 4\.2/si' => 'Android 4.2 Jelly Bean',
				'/Android 4\.3/si' => 'Android 4.3 Jelly Bean',
				'/Android 4\.4/si' => 'Android 4.4 KitKat',
				'/Android 5\.0/si' => 'Android 5.0 lollipop', // Ajouté 2015-06-20
				'/Android 5\.1/si' => 'Android 5.1 lollipop', // Ajouté 2015-07-30
				'/Android 6\.0/si' => 'Android 6.0 marshmallow', // Ajouté 2015-11-11
				'/Android 1.0/si' => 'Android 1.0',
				'/Android 1.5/si' => 'Android 1.5 Cupcake',
				'/Android 1.6/si' => 'Android 1.6 Donut',
				'/Android 2.0|Android 2.1/si' => 'Android 2.0/1 Eclair',
				'/Android 2.2/si' => 'Android 2.2.x Froyo',
				'/Android 2.3|Android 2.4/si' => 'Android 2.3.x Gingerbread',
				'/Android 3./si' => 'Android 3.x Honeycomb',
				'/Android Donut/si' => 'Android 1.6 Donut',
				'/Android Eclair/si' => 'Android 2.0/1 Eclair',
				'/Android 4./si' => 'Android 4.0.x Ice Cream Sandwich',
				'/Android-4./si' => 'Android 4.0.x Ice Cream Sandwich',
				'/Android\/3/si' => 'Android 3.x Honeycomb',
				'/Android ([0-9\.]+)/si' => 'Android',
				'/Android.*Linux.*Opera Mobi/si' => 'Android',
				'/Android/si' => 'Android',
				'/^Mozilla.*Linux.*AppleWebKit.*Puffin\/[0-9\.]+AT|AP$/si' => 'Android',
				'/^Mozilla.*CrKey.*arm.*Chrome/si' => 'Android',
				'/^Opera.*Android/si' => 'Android',
			),
			'BlackBerry OS' => array(
				'/BlackBerry/si' => 'BlackBerry OS',
				'/^Mozilla.*BB10.*Touch.*AppleWebKit.*Mobile/si' => 'BlackBerry OS',
				'/^Mozilla.*BB10.*Kbd.*AppleWebKit.*Mobile/si' => 'BlackBerry OS',
			),
			'Haiku OS' => array(
				'/BeOS.*Haiku BePC/si' => 'Haiku OS',
			),
			'Tizen' => array(
				'/^Mozilla.*Tizen\/1/si' => 'Tizen 1',
				'/^Mozilla.*Tizen 2/si' => 'Tizen 2',
			),
			'JVM' => array(
				'/j2me/si' => 'JVM (Platform Micro Edition)',
				'/NetFront.*Profile\/MIDP/si' => 'JVM (Platform Micro Edition)',
				'/Obigo.*MIDP/si' => 'JVM (Platform Micro Edition)',
				'/Teleca.*MIDP/si' => 'JVM (Platform Micro Edition)',
				'/java\/[0-9a-z\.]+/si' => 'JVM (Java)',
				'/java[0-9a-z\.]+/si' => 'JVM (Java)',
			),
			'BSD' => array(
				'/.*netbsd.*/si' => 'NetBSD',
				'/.*freebsd.*/si' => 'FreeBSD',
				'/.*openbsd.*/si' => 'OpenBSD',
				'/^Mozilla.*PlayStation 4.*AppleWebKit/si' => 'Orbis OS',
				'/.*dragonfly.*/si' => 'DragonFly BSD',
			),
			'OS X' => array(
				'/Mac OS X (10_6|10\.6)/si' => 'OS X 10.6 Snow Leopard',
				'/Mac OS X (10_5|10\.5)/si' => 'OS X 10.5 Leopard',
				'/Mac OS X (10_4|10\.4)/si' => 'OS X 10.4 Tiger',
				'/Mac OS X (10_7|10\.7)/si' => 'OS X 10.7 Lion',
				'/Mac OS X (10_8|10\.8)/si' => 'OS X 10.8 Mountain Lion',
				'/Mac OS X (10_9|10\.9)/si' => 'OS X 10.9 Mavericks',
				'/AppleTV/si' => 'OS X',
				'/Mac OS X (10_10|10\.10)/si' => 'OS X 10.10 Yosemite',
				'/.*\/.*CFNetwork\/(1\.2\.1|1\.2\.2|1\.2\.6) Darwin\//si' => 'OS X 10.3 Panther',
				'/.*\/.*CFNetwork\/(128|128\.2|129\.5|129\.9|129\.10|129\.13|129\.16|129\.18|129\.20|129\.21|129\.22) Darwin\//si' => 'OS X 10.4 Tiger',
				'/.*\/.*CFNetwork\/(217|220|221\.5|330|330\.4|339\.5|422\.11|438\.12|438\.14) Darwin\//si' => 'OS X 10.5 Leopard',
				'/.*\/.*CFNetwork\/(454\.4|454\.5|454\.9\.4|454\.9\.7|454\.11\.5|454\.11\.12|454\.12\.4) Darwin\//si' => 'OS X 10.6 Snow Leopard',
				'/.*\/.*CFNetwork\/(520\.0\.13|520\.2\.5|520\.3\.2|520\.4\.3|520\.5\.1) Darwin\//si' => 'OS X 10.7 Lion',
				'/.*\/.*CFNetwork\/(596\.0\.1|596\.1|596\.2\.3|596\.3\.3|596\.4\.3|596\.5) Darwin\//si' => 'OS X 10.8 Mountain Lion',
				'/.*\/.*CFNetwork\/(673\.0\.3|673\.2\.1|673\.3|673\.4) Darwin\//si' => 'OS X 10.9 Mavericks',
				'/.*\/.*CFNetwork\/720\.0\.9\//si' => 'OS X 10.10 Yosemite',
				'/Mac OS X/si' => 'OS X',
				'/Darwin 10\.3/si' => 'OS X 10.3 Panther',
			),
			'Amiga OS' => array(
				'/amiga/si' => 'Amiga OS',
			),
			'IRIX' => array(
				'/irix/si' => 'IRIX',
			),
			'OpenVMS' => array(
				'/open.*vms/si' => 'OpenVMS',
			),
			'BeOS' => array(
				'/beos/si' => 'BeOS',
			),
			'OS/2' => array(
				'/os\/2.*warp/si' => 'OS/2 Warp',
				'/os\/2/si' => 'OS/2',
			),
			'RISK OS' => array(
				'/RISC.OS/si' => 'RISK OS',
				'/riscos/si' => 'RISK OS',
			),
			'HP-UX' => array(
				'/hp-ux/si' => 'HP-UX',
			),
			'Plan 9' => array(
				'/plan 9/si' => 'Plan 9',
			),
			'QNX' => array(
				'/QNX x86pc/si' => 'QNX x86pc',
			),
			'SCO' => array(
				'/SCO_SV/si' => 'SCO OpenServer',
			),
			'SkyOS' => array(
				'/SkyOS/si' => 'SkyOS',
			),
			'webOS' => array(
				'/webOS\/.*AppleWebKit/si' => 'webOS',
				'/Linux.*hpwOS/' => 'webOS',
			),
			'DangerOS' => array(
				'/Danger hiptop [0-9\.]+/si' => 'Danger Hiptop',
			),
			'XrossMediaBar (XMB)' => array(
				'/PLAYSTATION 3/si' => 'XrossMediaBar (XMB)',
				'/PlayStation Portable/si' => 'XrossMediaBar (XMB)',
			),
			'RIM OS' => array(
				'/RIM Tablet OS 1[0-9\.]+/si' => 'BlackBerry Tablet OS 1',
				'/RIM Tablet OS 2[0-9\.]+/si' => 'BlackBerry Tablet OS 2',
			),
			'Bada' => array(
				'/Bada\/[0-9\.]+/si' => 'Bada',
				'/^Opera.*Bada/si' => 'Bada',
			),
			'Inferno OS' => array(
				'/^Mozilla.*Charon.*Inferno/' => 'Inferno OS',
			),
			'LiveArea' => array(
				'/PlayStation Vita/si' => 'LiveArea',
			),
			'Firefox OS' => array(
				'/^Mozilla\/5\.0 \((Mobile|Tablet).*rv:[0-9\.]+.*\) Gecko\/[0-9\.]+ Firefox\/[0-9\.]+$/si' => 'Firefox OS',
			),
			'Nintendo' => array(
				'/^Mozilla.*Nintendo 3DS/si' => 'Nintendo 3DS',
				'/Mozilla.*compatible.*Nitro.*Opera/si' => 'Nintendo DS',
				'/Nintendo DS/si' => 'Nintendo DS',
			),
			'Sailfish' => array(
				'/Linux.*Jolla.*Sailfish/si' => 'Sailfish',
			),
			'Nintendo Wii' => array(
				'/Nintendo.WiiU/si' => 'Wii U OS',
				'/Nintendo.Wii/si' => 'Wii OS',
			),
			'MeeGo' => array(
				'/meego/si' => 'MeeGo',
			),
			'Mac OS' => array(
				'/mac_powerpc/si' => 'Mac OS',
				'/Macintosh/si' => 'Mac OS',
				'/powerpc\-apple/si' => 'Mac OS',
				'/os=Mac/si' => 'Mac OS',
				'/SO=MAC10,6/si' => 'Mac OS',
				'/so=Mac 10.5.8/si' => 'Mac OS',
				'/macos/si' => 'Mac OS',
				'/Darwin/si' => 'Mac OS',
			),
			'AIX' => array(
				'/aix/si' => 'AIX',
			),
			'Syllable' => array(
				'/Syllable/si' => 'Syllable',
			),
			'AROS' => array(
				'/AROS/si' => 'AROS',
			),
			'MINIX' => array(
				'/Minix 3/si' => 'MINIX 3 ',
			),
			'Brew' => array(
				'/brew/si' => 'Brew',
			),
		);

		/**
		 * Tableau d'expressions régulières de périphériques, regroupés par types
		 * 
		 * @var array
		 */
		private static $devices = array(
			'Game console' => array(
				'/^Mozilla.*Android.*OUYA/si',
				'/^Mozilla.*compatible.*Nitro.*Opera/si',
				'/^Mozilla.*Android.*ARCHOS GAMEPAD.*AppleWebKit/si',
				'/Playstation/si',
				'/Nintendo/si',
				'/xbox/si',
			),
			'Smartphone' => array(
				'/^Mozilla.*Tizen\/[0-9\.]+/si',
				'/^Mozilla.*Windows Phone.*ARM.*NOKIA.*Lumia 820/si',
				'/^Mozilla.*Windows Phone.*ARM.*NOKIA.*Lumia 920/si',
				'/^Mozilla\/.*Ubuntu.*Mobile.*WebKit/si',
				'/^BlackBerry[0-9]+.*Profile\/MIDP/si',
				'/Bada\/[0-9\.]+/si',
				'/iPhone.*OS/si',
				'/Obigo.*MIDP/si',
				'/Teleca.*MIDP/si',
				'/.*\/.*CFNetwork\/(485\.2|485\.10\.2|485\.12\.7|485\.12\.30|485\.13\.9) Darwin\//si',
				'/.*\/.*CFNetwork\/(602|609|609\.1\.4) Darwin\//si',
				'/.*\/.*CFNetwork\/(672\.0\.2|672\.0\.8|672\.1\.12|672\.1\.13|672\.1\.14|672\.1\.15) Darwin\//si',
				'/.*\/.*CFNetwork\/459 Darwin\//si',
				'/.*\/.*CFNetwork\/(548\.0\.3|548\.0\.4|548\.1\.4) Darwin\//si',
				'/j2me/si',
			),
			'Smart TV' => array(
				'/^HbbTV/si',
				'/^Mozilla.*Android.*POV_TV-HDMI.* Safari/si',
				'/^Mozilla.*DTV.*AppleWebKit.*Espial/si',
				'/^Mozilla.*WebTV.*MSIE/si',
				'/^Mozilla.*AppleWebKit.*LG Browser.*LG NetCast/si',
				'/^Opera.*Linux.*NETTV\/[0-9\.]+.*Presto/si',
				'/^Mozilla.*Gecko.*Maple [0-9\.]+/si',
				'/^Opera.*Linux.*Opera TV/si',
				'/^Opera.*Linux.*SC\/IHD92 STB/si',
				'/^Mozilla.*CrKey.*arm.*Chrome/si',
				'/^Mozilla.*Android.*GTV100.*Safari/si',
				'/^Mozilla.*FreeBSD.*Viera.*AppleWebKit.*Viera.*Chrome/si',
				'/^Mozilla.*Escape [0-9\.]+/si',
				'/^Mozilla.*Gecko.*Firefox.*Kylo\/([0-9\.]+)$/si',
				'/^Mozilla.*(Chrome.*GoogleTV|GoogleTV.*Chrome)/si',
				'/^Opera.*Linux.*HbbTV/si',
				'/^Mozilla.*Android.*SMARTTV/si',
				'/^Mozilla.*SmartHub.*Linux/si',
				'/^Mozilla.*Linux.*HbbTV/si',
				'/AppleTV/si',
				'/^Roku\//si',
				'/InettvBrowser/si',
			),
			'Wearable computer' => array(
				'/^Mozilla.*Android.*Glass 1.*AppleWebKit.*Mobile Safari/si',
			),
			'Tablet' => array(
				'/^Mozilla.*linux.*KFAPWI.*Silk/si',
				'/^Mozilla.*Linux.*AppleWebKit.*Puffin\/[0-9\.]+(AT|IT)$/si',
				'/^Mozilla.*Android.*Hudl HT7S3.*AppleWebKit.*Safari/si',
				'/^Mozilla.*Tablet.*rv.*Gecko.*Firefox.*/si',
				'/^Mozilla.*linux.*KFSOWI.*Silk/si',
				'/^Mozilla\/.*Ubuntu.*Tablet.*WebKit/si',
				'/^Mozilla.*linux.*KFAPWA.*Silk/si',
				'/^Mozilla.*linux.*KFTHWA.*Silk/si',
				'/^Mozilla.*linux.*KFTHWI.*Silk/si',
				'/^Mozilla.*Android.*ThinkPad Tablet/si',
				'/^Mozilla.*Android.*Nexus 7/si',
				'/^Mozilla.*Android.*bq (Edison|Darwin|Voltaire)/si',
				'/^Mozilla.*Android.*(PMID701C|PMID701i|PMID705|PMID706|PMID70DC|PMID70C|PMID720|PTAB1050|ptab750|PTAB7200|ptab4300|pmid920)/si',
				'/^Mozilla.*Android.*(K00C|K00U|K00Z|K00L|K00F|ME302KL|ME302C|ME301T|ME173X|ME172V)/si',
				'/^Mozilla.*Android.*(CT1002|CT1001H|CT710|CT1010)/si',
				'/^Mozilla.*Android.*(M470BSA|M470BSE|E270BSA|M470BSE|M470BSA)/si',
				'/^Mozilla.*Android.*QMV7A/si',
				'/^Mozilla.*Android.*(MIDC497|MIDC700|MIDC800|MIDC801|MIDC802|MIDC901|PMID1000|PMID4311|PMID4312|PMID700)/si',
				'/^Mozilla.*Android.*(PTAB1050|PMID701i|MIDC409|MID0704|MID0714|MID0734|MID0738|MIDC010|MIDC407|MIDC409|MIDC410)/si',
				'/^Mozilla.*Android.*(TV pad|TVPAD)/si',
				'/^Mozilla.*Android.*Maxwell (Lite|Plus)/si',
				'/^Mozilla.*Android.*TegraNote\-P1640/si',
				'/^Mozilla.*Android.*(PMP5570C|PMP5588C)/si',
				'/^Mozilla.*Android.*(HTC PG09410|HTC_PG09410|PG41200)/si',
				'/^Mozilla.*Android.*(Nook.*Color|BNRV200|BNTV250|BNTV300|BNTV400|BNTV600)/si',
				'/^Mozilla.*Android.* (HP Slate 10 HD|HP Slate 7|HP SlateBook 10)/si',
				'/^Mozilla.*Android.*(TAB.*Xenta|TAB.*Luna|TAB.*Build\/GRI40|TAB.*Build\/IML74K|TAB.*Build\/IML74K)/si',
				'/^Mozilla.*Android.*(A701|A210|A211|A100|A101|A1\-811|A1\-713HD|A3\-A11|b1\-720|B1\-A71|B1\-710|B1\-711|a510s)/si',
				'/^Mozilla.*Android.*SM\-(P600|P605)/si',
				'/^Mozilla.*linux.*KFJWI.*Silk/si',
				'/^Mozilla.*Android.*ViewPad 10/si',
				'/^Mozilla.*Android.*SmartTab/si',
				'/^Mozilla.*Android.* Enjoy/si',
				'/^Mozilla.*Android.*SurfTab/si',
				'/^Mozilla.*MSIE.*Windows.* Tablet PC [0-9\.]+/si',
				'/^Mozilla.*linux.*(Kindle Fire|KFOT|KFTT|KFJWI|KFJWA|KFTHWI|KFAPWI|KFSOWI).*Silk/si',
				'/^Mozilla.*Android.*Sony Tablet P/si',
				'/^Mozilla.*Android.*SGPT12/si',
				'/^Mozilla.*Android.*A80KSC/si',
				'/iPad.*OS/si',
				'/^Mozilla.*Android.*Transformer/si',
				'/^Mozilla.*Linux.*Kindle\/[0-9\.]+/si',
				'/^Mozilla.*Android.*GT\-(N8000|N8005|N8010|N8013|N8020)/si',
				'/^Mozilla.*Android.*GT\-(P1000|P1010|P3100|P3105|P3110|P3113|P5100|P5110|P5113|P5200|P5210|P6200|P6201|P6210|P6211|P6800|P6810|P7110|P7300|P7310|P7320|P7500|P7510|P7511)/si',
				'/^Mozilla.*Android.*SPH\-P500/si',
				'/^Mozilla.*Android.*(MZ505|MZ601|MZ603|MZ604|MZ605|MZ606|MZ607|MZ608|MZ609|MZ616|MZ617)/si',
				'/^Mozilla.*Android.*Xoom/si',
				'/^Mozilla.*Android.*LG\-(F200K|F200L|F200S)/si',
				'/^Mozilla.*Android.*(HUAWEI MediaPad|MediaPad 10)/si',
				'/^Mozilla.*Android.*HTC PG09410/si',
				'/^Mozilla.*Android.*Nexus 10/si',
				'/^Mozilla.*Android.*L-06C Build/si',
				'/^Mozilla.*hp-tablet.*hpwOS.*TouchPad/si',
				'/^Mozilla.*Android.*SCH\-(I925|I915)/si',
				'/^Mozilla.*Android.*Obreey SURFpad/si',
				'/^Mozilla.*Android.*SGH\-(I957M|I497|I467)/si',
				'/^Mozilla.*Android.*SHV\-(E230|E140)/si',
				'/^Mozilla.*Android.*(PocketBook A10|PocketBook A7)/si',
				'/^Mozilla.*Android.*SHW\-(M380|M480K|M500|M305)/si',
				'/Kindle Fire/si',
				'/(PlayBook|RIM Tablet)/si',
				'/^Mozilla.*Android.*S7/si',
				'/^Mozilla.*Silk.*Safari/si',
				'/Opera Tablet/si',
				'/^Mozilla.*Android.*Tablet.*AppleWebKit/si',
			),
			'PDA' => array(
				'/^Mozilla.*Windows CE.*IEMobile.*HPiPA.*PPC/si',
				'/^Mozilla.*MSIE.*Windows CE.*HP iPAQ/si',
				'/^Mozilla.*PalmOS.*WebPro.*Palm/si',
				'/PalmSource.*Blazer/si',
			),
			'Other' => array(
				'/^Mozilla.*AppleWebKit.*QtCarBrowser.*Safari/si',
			),
		);

		/**
		 * Tableau d'expressions régulières de navigateurs en fonction de leur nom, regroupés par types
		 * 
		 * @var array
		 */
		private static $browsers = array(
			'Mobile Browser' => array(
				'/^Mozilla.*Android.*AppleWebKit.*Chrome.*OPR\/([0-9\.]+)/si' => 'Opera Mobile',
				'/^Mozilla.*Android.*AppleWebKit.*FlyFlow\/([0-9\.]+).*Mobile Safari/si' => 'Baidu mobile browser',
				'/^Mozilla.*Android.*AppleWebKit.*Mobile Safari.*bdbrowser_i18n\/([0-9\.]+)/si' => 'Baidu mobile browser',
				'/^Mozilla.*Android .*Ninesky\-android\-mobile\/([0-9\.]+)/si' => 'NineSky',
				'/mozilla.*AppleWebKit.*NetFrontLifeBrowser\/([0-9\.]+)/si' => 'NetFront Life',
				'/^Mozilla.*Android.*AppleWebKit.*Maxthon\/([0-9\.]+)/si' => 'Maxthon mobile',
				'/^Mozilla.*Android.*AppleWebKit.*Maxthon \(([0-9\.]+)/si' => 'Maxthon mobile',
				'/^Mozilla.*Android.*AppleWebKit.*MxBrowser\/([0-9\.]+)/si' => 'Maxthon mobile',
				'/^Mozilla.*Linux.*Kindle\/([0-9\.]+)/si' => 'Kindle Browser',
				'/^Mozilla.*Silk\/([0-9\.\-]+).*safari/si' => 'Silk',
				'/^Mozilla.*Android.*AppleWebKit.*Chrome.*YaBrowser\/([0-9\.]+).*Mobile/si' => 'Yandex.Browser mobile',
				'/^Mozilla.*AppleWebKit.*YaBrowser\/([0-9\.]+) Mobile.*Safari/si' => 'Yandex.Browser mobile',
				'/mozilla.*Blazer\/([0-9a-z\+\-\.]+)/si' => 'Blazer',
				'/IEMobile ([0-9\.]+)/si' => 'IE Mobile',
				'/Mozilla.*Android.*AppleWebKit.*CrMo\/([0-9\.]+)/si' => 'Chrome Mobile',
				'/^Mozilla.*AppleWebKit.*Chrome\/([0-9\.]+).*Mobile Safari/si' => 'Chrome Mobile',
				'/^Mozilla.*iPhone.*AppleWebKit.*CriOS\/([0-9\.]+).*Mobile.*Safari/si' => 'Chrome Mobile',
				'/^Mozilla.*iPad.*AppleWebKit.*CriOS\/([0-9\.]+).*Mobile.*Safari/si' => 'Chrome Mobile',
				'/^MQQBrowser\/([0-9\.]+)/si' => 'QQbrowser',
				'/^MQQBrowser\/(Mini[0-9\.]+)/si' => 'QQbrowser',
				'/^Mozilla.*ASUS Transformer Pad.*AppleWebKit.*Chrome\/([0-9\.]+).*Safari/si' => 'Chrome Mobile',
				'/Mozilla.*AppleWebKit.*Chrome.*Safari.*Puffin\/([0-9\.]+)/si' => 'Puffin',
				'/^Mozilla.*BB10.*Touch.*AppleWebKit.*Mobile/si' => 'BlackBerry Browser',
				'/^Mozilla.*BB10.*Kbd.*AppleWebKit.*Mobile/si' => 'BlackBerry Browser',
				'/Mozilla.*Linux.*Android.*WebKit.*Version\/([0-9\.]+)/si' => 'Android browser',
				'/BlackBerry/si' => 'BlackBerry Browser',
				'/Mozilla.*Linux.*webOS.*webOSBrowser\/([0-9\.]+)/si' => 'wOSBrowser',
				'/Blazer ([0-9\.]+)/si' => 'Blazer',
				'/^Mozilla\/.*webOS\/[0-9\.]+.*AppleWebKit.*Pre\/([0-9\.]+)$/si' => 'Palm Pre web browser',
				'/^Mozilla.*AppleWebKit.*SLP Browser\/([0-9\.]+)/si' => 'Tizen Browser',
				'/^Mozilla.*AppleWebKit.*(Tizen Browser|Tizenbrowser)\/([0-9\.]+)/si' => 'Tizen Browser',
				'/^Mozilla.*Playstation Vita.*AppleWebKit.*Silk\/([0-9\.]+)/si' => 'PS Vita browser',
				'/^Mozilla.*Tizen 2.*Version\/([0-9\.]+).*Mobile Safari/si' => 'Tizen Browser',
				'/^Mozilla.*mac.*AppleWebKit.*Coast\/([0-9\.]+)/si' => 'Coast',
				'/mozilla.*rv:[0-9\.]+.*gecko\/[0-9]+.*fennec\/([0-9a-z\+\-\.]+).*/si' => 'Mobile Firefox',
				'/^Mozilla.*AppleWebKit.*Skyfire\/([0-9\.]+)/si' => 'Skyfire',
				'/Symbian.* NokiaBrowser/si' => 'Nokia Web Browser',
				'/^Mozilla.*Mobile.*rv.*Gecko.*Firefox\/([0-9\.]+)/si' => 'Mobile Firefox',
				'/^Mozilla.*AppleWebKit.*UCBrowser\/([0-9\.]+).*Mobile.*Safari/si' => 'UC Browser',
				'/^Mozilla.*Windows Phone.*Trident.*IEMobile\/([0-9\.]+)/si' => 'IE Mobile',
				'/^Mozilla.*Tablet.*rv.*Gecko.*Firefox\/([0-9\.]+)/si' => 'Mobile Firefox',
				'/^Mozilla.*NokiaBrowser\/([0-9\.]+).*Mobile Safari/si' => 'Nokia Web Browser',
				'/^Mozilla.*like Mac OS X.*AppleWebKit.*OPiOS\/([0-9\.]+).* Mobile/si' => 'Opera Mini',
				'/Opera\/.*Opera Mini\/([0-9\.]+)/si' => 'Opera Mini',
				'/^Mozilla.*Dolfin\/([0-9\.]+)/si' => 'Dolphin',
				'/mozilla.*applewebkit.*version\/([0-9a-z\+\-\.]+).*mobile.*safari\/[0-9a-z\+\-\.]+.*/si' => 'Mobile Safari',
				'/^Mozilla.*MSIE.*Windows Phone.*IEMobile\/([0-9\.]+)/si' => 'IE Mobile',
				'/^Mozilla.* AppleWebKit.*Mobile/si' => 'Mobile Safari',
				'/^Mozilla.*RIM Tablet OS.*AppleWebKit.*Safari/si' => 'BlackBerry Browser',
				'/Opera mobi.*Version\/([0-9\.]+)/si' => 'Opera Mobile',
				'/Opera Mobi.*Opera ([0-9\.]+)/si' => 'Opera Mobile',
				'/Opera ([0-9\.]+).*Opera Mobi/si' => 'Opera Mobile',
				'/mozilla.*iphone.*os.*/si' => 'Mobile Safari',
				'/mozilla.*ipad.*os.*/si' => 'Mobile Safari',
				'/Opera.*Opera Tablet.*Presto.*Version\/([0-9\.]+)/si' => 'Opera Mobile',
				'/HTC.*Opera\/([0-9\.]+).*Windows/si' => 'Opera Mobile',
				'/Opera.*Opera Mobi/si' => 'Opera Mobile',
				'/nokiac3.*safari/si' => 'Mobile Safari',
				'/mozilla.*Linux armv7l.*rv:[0-9\.]+.*gecko\/[0-9]+.*Tablet browser ([0-9a-z\+\-\.]+).*/si' => 'MicroB',
				'/series60.*applewebkit.*/si' => 'Nokia Web Browser',
				'/mozilla.*Linux armv7l.*rv:[0-9\.]+.*gecko\/[0-9]+.*maemo browser ([0-9a-z\+\-\.]+).*/si' => 'MicroB',
				'/mozilla.*gecko\/[0-9]+.*minimo\/([0-9a-z\+\-\.]+).*/si' => 'Minimo',
				'/UP\.Browser\/([0-9a-zA-Z\.]+).*/s' => 'Openwave Mobile Browser',
				'/UP\/([0-9a-zA-Z\.]+).*/s' => 'Openwave Mobile Browser',
				'/NetFront\/([0-9a-z\.]+).*/si' => 'NetFront',
				'/Openwave/si' => 'Openwave Mobile Browser',
				'/doris\/([0-9a-z\+\-\.]+).*/si' => 'Doris',
				'/NetFront([0-9a-z\.]+).*/si' => 'NetFront',
				'/NF-Browser\/([0-9\.]+)/si' => 'NetFront',
				'/^Mozilla\/.*AppleWebKit.*TeaShark\/([0-9\.]+)$/si' => 'TeaShark',
				'/NokiaN93/si' => 'Nokia Web Browser',
				'/Nokia.*SymbianOS.*Series60/si' => 'Nokia Web Browser',
				'/SymbianOS.*Series60.*Nokia.*AppleWebKit/si' => 'Nokia Web Browser',
				'/^Mozilla\/.*Linux.*AppleWebKit.*tear/si' => 'Tear',
				'/^SAMSUNG.*Jasmine\/([0-9\.]+)/si' => 'Jasmine',
				'/^Mozilla\/.*uZardWeb\/([0-9\.]+)/si' => 'uZard Web',
				'/^Mozilla.*Android.*GoBrowser\/([0-9\.]+)/si' => 'GO Browser',
				'/^Mozilla.*Android.*GoBrowser/si' => 'GO Browser',
				'/Mozilla.*Linux.*hpwOS.*wOSBrowser\/([0-9\.]+)/si' => 'wOSBrowser',
				'/^AtomicBrowser\/([0-9\.]+).*CFNetwork/si' => 'Atomic Web Browser',
				'/^Mozilla.*Polaris ([0-9\.])/si' => 'Polaris',
				'/^Mozilla\/5\.0.*SymbianOS\/[0-9\.]+.*AppleWebKit.*KHTML.*Safari\/[0-9\.]+/si' => 'Nokia Web Browser',
				'/^Mozilla.*PalmOS.*WebPro\/([0-9\.]+).*Palm/si' => 'Palm WebPro',
				'/^OneBrowser\/([0-9\.]+).*Android.*AppleWebKit/si' => 'ONE Browser',
				'/^Mozilla\/.*Ubuntu.*(Tablet|Mobile).*WebKit/si' => 'Ubuntu web browser',
				'/^Mozilla.*MSIE.*Windows (CE|Phone)/si' => 'IE Mobile',
				'/^MobileSafari\/[0-9\.]+ CFNetwork\/[0-9\.]+ Darwin\/[0-9\.]+/' => 'Mobile Safari',
				'/^MOT.*MIB\/([0-9\.]+)/si' => 'Motorola Internet Browser',
				'/^Samsung-[a-zA-Z09]+.*AU-MIC-[a-zA-Z0-9]+\/([0-9\.]+)/si' => 'Obigo',
				'/^SonyEricsson.*SEMC-Browser\/([0-9\.]+)/si' => 'SEMC Browser',
				'/Browser\/Teleca|Teleca\/.*MIDP/si' => 'Obigo',
				'/^Mozilla.*Danger hiptop/si' => 'NetFront',
				'/\/GoBrowser\/([0-9\.]+)/si' => 'GO Browser',
				'/^Mozilla.*MSIE ([0-9\.]+).*XBLWP7/si' => 'IE Mobile',
				'/Polaris\/([0-9\.]+)/si' => 'Polaris',
				'/.*Obigo Browser ([0-9\.]+)/si' => 'Obigo',
				'/^Mozilla.*Symbian OS.*Obigo/si' => 'Obigo',
				'/^Mozilla.*Teleca Q7.*Brew/si' => 'Obigo',
				'/^DoCoMo\//si' => 'NetFront',
				'/mozilla.*PlayStation\ Portable.*/si' => 'NetFront',
				'/Obigo.*Profile\/MIDP/si' => 'Obigo',
				'/UCWEB/si' => 'UC Browser',
				'/PLAYSTATION 3/si' => 'NetFront',
				'/^SAMSUNG.*Dolfin\/([0-9\.]+)/si' => 'Dolphin',
				'/^OneBrowser\/([0-9\.]+)/si' => 'ONE Browser',
				'/ObigoInternetBrowser/si' => 'Obigo',
				'/MSIE ([0-9a-z\+\-\.]+).*windows ce/si' => 'IE Mobile',
			),
			'Multimedia Player' => array(
				'/^FlyCast\/([0-9\.]+)/si' => 'FlyCast',
				'/Miro\/([0-9a-z\-\.]+).*http:\/\/www\.getmiro\.com\//si' => 'Miro',
				'/boxee.*\(.*\ ([0-9a-zA-Z\.]+)\)/si' => 'Boxxe',
				'/^XBMC\/([0-9a-z\.\-]+)/si' => 'XBMC',
				'/^Democracy\/([0-9\.]+)/si' => 'Miro',
				'/mozilla.*rv:[0-9\.]+.*gecko.*Songbird\/([0-9\.]+)/si' => 'Songbird',
				'/^RSS_Radio ([0-9\.]+)$/si' => 'RSS Radio',
				'/^CorePlayer.*CorePlayer\/([0-9\._]+)$/si' => 'CorePlayer',
				'/^Banshee ([0-9a-z\.]+).*http:\/\/banshee-project\.org/si' => 'Banshee',
				'/^foobar2000\/([0-9a-z\._]+$)/si' => 'foobar2000',
				'/^GomPlayer ([0-9, ]+)/si' => 'GOM Player',
				'/^XMPlay\/([0-9\.]+)$/si' => 'XMPlay',
				'/^PublicRadioPlayer\/([0-9\.]+)/si' => 'Public Radio Player',
				'/^PublicRadioApp\/([0-9\.]+)/si' => 'Public Radio Player',
				'/^PocketTunes\/([0-9a-z\.]+)$/si' => 'Pocket Tunes',
				'/^Plex\/([0-9\.]+).*plexapp\.com/si' => 'Plex Media Center',
				'/^WAFA\/([0-9\.]+).*Android/si' => 'Winamp for Android',
				'/^SubStream\/([0-9\.]+).* CFNetwork/si' => 'SubStream',
				'/^MPlayer\//si' => 'MPlayer',
				'/^Windows\-Media\-Player\/([0-9\.]+)$/si' => 'Windows Media Player',
				'/^VLC media player \- version ([0-9a-z\-\.]+) .* VideoLAN team$/si' => 'VLC media player',
				'/^QuickTime\/([0-9\.]+)/' => 'QuickTime',
				'/^QuickTime.*qtver=([0-9\.a-z]+)/si' => 'QuickTime',
				'/^MPlayer ([0-9\.]+)/si' => 'MPlayer2',
				'/^Plex\/([0-9\.]+).*Android/si' => 'Plex Media Center',
				'/^NSPlayer\/([0-9\.]+)/si' => 'Windows Media Player',
				'/^iTunes\/([0-9\.]+)/si' => 'iTunes',
			),
			'Browser' => array(
				'/mozilla.*rv:[0-9\.]+.*gecko\/[0-9]+.*camino\/([0-9a-z\+\-\.]+).*/si' => 'Camino',
				'/mozilla.*gecko.*kazehakase\/([0-9a-z\+\-\.]+).*/si' => 'Kazehakase',
				'/mozilla.*rv:[0-9\.]+.*gecko\/[0-9]+.*firefox\/([0-9a-z\+\-\.]+).*swiftfox/si' => 'Swiftfox',
				'/mozilla.*rv:[0-9\.]+.*gecko.*kapiko\/([0-9a-z\+\-\.]+).*/si' => 'Kapiko',
				'/^Mozilla.*Windows.*Trident\/.*Avant Browser.*11.*like Gecko/si' => 'Avant Browser',
				'/mozilla.*gecko\/[0-9]+.*epiphany\/([0-9a-z\+\-\.]+).*/si' => 'Epiphany',
				'/^Mozilla.*Windows.*Gecko.*Polarity\/([0-9\.]+)/si' => 'Polarity',
				'/mozilla.*Lunascape\/([0-9a-z\+\-\.]+).*/si' => 'Lunascape',
				'/mozilla.*applewebkit.*arora\/([0-9a-z\+\-\.]+).*/si' => 'Arora',
				'/Bolt\/([0-9\.]+)/si' => 'Bolt',
				'/Demeter\/([0-9\.]+)/si' => 'Demeter',
				'/mozilla.*applewebkit.*fluid\/([0-9a-z\+\-\.]+).*/si' => 'Fluid',
				'/Hv3\/([0-9a-z\.])/si' => 'Hv3',
				'/Cheshire\/([0-9a-z\.]+)/si' => 'Cheshire',
				'/mozilla.*rv:[0-9\.]+.*gecko.*CometBird\/([0-9a-z\+\-\.]+).*/si' => 'CometBird',
				'/mozilla.*rv:[0-9\.]+.*gecko.*IceCat\/([0-9a-z\+\-\.]+).*/si' => 'IceCat',
				'/mozilla.*applewebkit.*Stainless\/([0-9a-z\+\-\.]+).*safari/si' => 'Stainless',
				'/mozilla.*Comodo_Dragon\/([0-9a-z\+\-\.]+).*/si' => 'Comodo Dragon',
				'/^Mozilla.*Gecko.*Strata\/([0-9\.]+)/si' => 'Kirix Strata',
				'/^Mozilla.*rv:[0-9\.]+.*Gecko.*Firefox.*LBrowser\/([0-9a-z\-\.]+)/si' => 'LBrowser',
				'/^Mozilla.*Windows.*AppleWebKit.*MiniBrowser\/([0-9\.]+)/si' => 'Mini Browser',
				'/^Mozilla.*AppleWebKit.*Element Browser ([0-9\.]+)/si' => 'Element Browser',
				'/^Mozilla\/.*Gecko\/.*Firefox\/.*Kylo\/([0-9\.]+)$/si' => 'Kylo',
				'/mozilla.*AppleWebKit\/.*epiphany\/([0-9a-z\+\-\.]+).*/si' => 'Epiphany',
				'/Mozilla.*AppleWebKit.*KHTML.*SlimBoat\/([0-9\.]+)/si' => 'SlimBoat',
				'/^Mozilla.*AppleWebKit.*Chrome.*Beamrise\/([0-9\.]+)/si' => 'Beamrise',
				'/^Mozilla.*AppleWebKit.*Beamrise\/([0-9\.]+).*Chrome/si' => 'Beamrise',
				'/^Mozilla.*AppleWebKit.*Chrome.*YaBrowser\/([0-9\.]+)/si' => 'Yandex.Browser',
				'/^Mozilla.*AppleWebKit.*Superbird\/([0-9\.]+)/si' => 'Superbird',
				'/^Mozilla.*AppleWebKit.*YaBrowser\/([0-9\.]+).*Chrome/si' => 'Yandex.Browser',
				'/Mozilla.*Windows NT 6\..*Trident\/7\.0.*rv:([0-9\.]+)/si' => 'IE',
				'/Mozilla.*AppleWebKit.*Roccat\/([0-9\.]+).*R/si' => 'Roccat browser',
				'/^Mozilla.*AppleWebKit.*Chrome\/([0-9\.]+).*Safari.*MRCHROME/si' => 'Amigo',
				'/^Mozilla.*Windows.*Avant TriCore.*AppleWebKit.*Chrome.*Safari/si' => 'Avant Browser',
				'/^Mozilla.*Windows.*Avant TriCore.*Gecko.*Firefox/si' => 'Avant Browser',
				'/^Mozilla.*AppleWebKit.*Chrome\/[0-9\.]+.*Kinza\/([0-9\.]+)/si' => 'Kinza',
				'/^Mozilla.*AppleWebKit.*Chrome.*WebExplorer\/([0-9\.]+)/si' => 'Web Explorer',
				'/^Mozilla.*AppleWebKit.*MxNitro\/([0-9\.]+).*Safari/si' => 'MxNitro',
				'/^Mozilla.*MSIE.*Windows.*Tjusig ([0-9\.]+)/si' => 'Tjusig',
				'/^Mozilla.*MSIE.*Windows.*SiteKiosk ([0-9\.]+)/s' => 'SiteKiosk',
				'/Mozilla.*AppleWebKit.*WeltweitimnetzBrowser\/([0-9\.]+)/si' => 'Weltweitimnetz Browser',
				'/^Mozilla.*Chromium\/([0-9a-z\+\-\.]+).*chrome.*/si' => 'Chromium',
				'/Mozilla\/.*AppleWebKit.*Columbus\/([0-9\.]+)/si' => 'Columbus',
				'/Mozilla.*AppleWebKit.* WebRender/si' => 'WebRender',
				'/Mozilla.*Chrome.*CoolNovo\/([a-z0-9\.]+)/si' => 'CoolNovo',
				'/Mozilla.*Mac.*rv.*Gecko.*Firefox\/([0-9a-b\.]+).*TenFourFox/si' => 'TenFourFox',
				'/Mozilla.*Windows.*Gecko.*Firefox.*AvantBrowser\/Tri-Core/si' => 'Avant Browser',
				'/Mozilla.*AppleWebKit.*zBrowser\/SpringSun-([0-9\.]+)/si' => 'zBrowser',
				'/Mozilla.*AppleWebKit.*zBrowser\/NigtSky-([0-9\.]+)/si' => 'zBrowser',
				'/^Mozilla.*AppleWebKit.*YRCWeblink\/([0-9\.]+).*Safari/si' => 'YRC Weblink',
				'/^Mozilla.*AppleWebKit.*Chrome.*OPR\/([0-9\.]+)/si' => 'Opera',
				'/Mozilla.*Gecko.*Firefox.*IceDragon\/([0-9\.]+)/si' => 'IceDragon',
				'/^Mozilla.*AppleWebKit.*Chrome.*Safari.*Midori\/([0-9\.]+)/si' => 'Midori',
				'/^Mozilla.*AppleWebKit.*Dooble\/([0-9\.]+).*Safari/si' => 'Dooble',
				'/Mozilla.*AppleWebKit.*Chrome.*CoRom\/([0-9\.]+) Safari/si' => 'CoRom',
				'/^Mozilla.*Windows.*Gecko.*Firefox.*Waterfox\/([0-9\.]+)/si' => 'Waterfox',
				'/^Mozilla.*AppleWebKit.*Chrome.*Spark\/([0-9]+)/si' => 'Baidu Spark',
				'/^Mozilla.*AppleWebKit.*Chrome.*Nichrome\/self\/([0-9]+)/si' => 'Rambler browser',
				'/^Mozilla.*Windows NT 6\.4.*Chrome.*Safari.*Edge\/(12)/si' => 'IE',
				'/^Mozilla.*Windows NT.*AppleWebKit.*Chrome.*Safari.*Edge\/([0-9\.]+)/si' => 'Microsoft Edge', // Ajouté 2015-07-30
				'/^Mozilla.*AppleWebKit.*Oupeng\/([0-9\.]+)/si' => 'Opera',
				'/mozilla.*gecko\/[0-9]+.*flock\/([0-9a-z\+\-\.]+).*/si' => 'Flock',
				'/mozilla.*applewebkit.*iron\/([0-9a-z\+\-\.]+).*/si' => 'Iron',
				'/mozilla.*applewebkit.*shiira\/([0-9a-z\+\-\.]+).*safari/si' => 'Shiira',
				'/mozilla.*rv:[0-9\.]+.*gecko\/[0-9]+.*IceWeasel\/([0-9a-z\+\-\.]+).*/si' => 'IceWeasel',
				'/mozilla.*Maxthon ([0-9a-z\+\-\.]+)/si' => 'Maxthon',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*MyIE2/si' => 'Maxthon',
				'/^Mozilla.*AppleWebKit.*Shiira\/([0-9a-zA-z\.\-]+)/si' => 'Shiira',
				'/Mozilla.*Chrome.*Sleipnir\/([0-9\.]+)/si' => 'Sleipnir',
				'/mozilla..*lobo\/([0-9a-z\+\-\.]+).*/si' => 'Lobo',
				'/mozilla.*applewebkit.*shiira.*safari/si' => 'Shiira',
				'/mozilla.*firefox.*orca\/([0-9a-z\+\-\.]+).*/si' => 'Orca',
				'/^Mozilla\/.*Gecko.* Firefox.*Wyzo\/([0-9a-z\.]+)/si' => 'Wyzo',
				'/^Mozilla\/.*AppleWebKit\/.*Maxthon\/([0-9\.]+)/si' => 'Maxthon',
				'/mozilla.*rv:[0-9\.]+.*gecko.*GlobalMojo\/([0-9a-z\+\-\.]+).*/si' => 'GlobalMojo',
				'/mozilla.*rv:[0-9\.]+.*gecko.*Palemoon\/([0-9a-z\+\-\.]+).*/si' => 'Pale Moon',
				'/mozilla.*flock\/([0-9\.]+).*chrome/si' => 'Flock',
				'/mozilla.*rv:[0-9\.]+.*gecko.*myibrow\/([0-9a-z\.]+)/si' => 'My Internet Browser',
				'/^Mozilla.*Escape ([0-9\.]+)/si' => 'Espial TV Browser',
				'/^Mozilla.*Windows.*UltraBrowser ([0-9\.]+)/si' => 'UltraBrowser ',
				'/^Mozilla.*BrowseX \(([0-9\.]+)/si' => 'BrowseX',
				'/^Mozilla\/.*Gecko.*lolifox\/([0-9\.]+)/si' => 'lolifox',
				'/Mozilla.*AppleWebKit.*RockMelt\/([0-9a-z\.]+)/si' => 'RockMelt',
				'/^Mozilla.*rv:[0-9\.]+.*gecko\/[0-9]+.*Epic\/([0-9\.]+)/si' => 'Epic',
				'/Mozilla.*AppleWebKit.*InternetSurfboard\/([0-9\.a-z]+)/si' => 'InternetSurfboard',
				'/^Mozilla.*Gecko.*Vonkeror\/([0-9\.]+)/si' => 'Vonkeror',
				'/^Mozilla.*WebKi.*BlackHawk\/([0-9\.]+).*Chrome/si' => 'BlackHawk',
				'/^Mozilla\/.*Treco.*Fireweb Navigator\/([0-9a-z\.]+)/si' => 'Fireweb Navigator',
				'/Mozilla.*Gecko.*Sundial\/([0-9a-z_\.]+)/si' => 'Sundial',
				'/Mozilla.*Gecko.*Alienforce\/([0-9a-z\.]+)/si' => 'Alienforce',
				'/Mozilla.*AppleWebKit.*Chrome.*baidubrowser\/([0-9a-z\.]+)/si' => 'Baidu Browser',
				'/Mozilla.*MSIE.*Windows.*baidubrowser ([0-9a-z\.]+)/si' => 'Baidu Browser',
				'/Mozilla.*AppleWebKit.*Chrome.*SE ([0-9a-z\.]+) MetaSr/si' => 'Sogou Explorer',
				'/Mozilla.*MSIE.* Windows.*SE ([0-9a-z\.]+) MetaSr/si' => 'Sogou Explorer',
				'/^Mozilla.*AppleWebKit.*Chrome.*ZipZap ([0-9\.]+)/si' => 'ZipZap',
				'/Mozilla.*AppleWebKit.*QupZilla\/([0-9a-z\.\-]+)/si' => 'QupZilla',
				'/Mozilla.*AppleWebKit.*Patriott::Browser\/([0-9\.]+)/si' => 'Patriott',
				'/^Mozilla.*AppleWebKit.*Qt\/[0-9\.]+.*Ryouko\/([0-9\.]+).*Safari/si' => 'Ryouko',
				'/^Mozilla.*AppleWebKit.*Chrome.*Perk\/([0-9\.]+)/si' => 'Perk',
				'/^Mozilla.*AppleWebKit.*WhiteHat Aviator\/([0-9\.]+) Chrome/si' => 'Aviator',
				'/^Mozilla.*Gecko.*Maple ([0-9\.]+)/si' => 'Maple browser',
				'/mozilla.*rv:[0-9\.]+.*Whistler.*myibrow\/([0-9a-z\.]+)/si' => 'My Internet Browser',
				'/mozilla.*rv:[0-9\.]+.*gecko.*Navigator\/([0-9a-z\+\-\.]+).*/si' => 'Netscape Navigator',
				'/Iceape\/([0-9a-zA-z\.\-]+)/si' => 'IceApe',
				'/mozilla.*chrome\/([0-9a-z\+\-\.]+).*/si' => 'Chrome',
				'/^Opera\/[0-9\.]+.*Presto\/[0-9\.]+.*Version\/([0-9\.]+)/si' => 'Opera',
				'/Mozilla\/.*Gecko.*Firefox.*Madfox\/([0-9a-z\.]+)/si' => 'Madfox',
				'/mozilla.*rv:[0-9\.]+.*gecko\/[0-9]+.*seamonkey\/([0-9a-z\+\-\.]+).*/si' => 'SeaMonkey',
				'/mozilla.*rv:[0-9\.]+.*gecko.*firefox\/([0-9a-z\+\-\.]+).*/si' => 'Firefox',
				'/mozilla.*netscape[0-9]?\/([0-9a-z\+\-\.]+).*/si' => 'Netscape Navigator',
				'/mozilla.*gecko\/[0-9]+.*galeon\/([0-9a-z\+\-\.]+).*/si' => 'Galeon',
				'/mozilla.*gecko\/[0-9]+.*k\-meleon\/([0-9a-z\+\-\.]+).*/si' => 'K-Meleon',
				'/mozilla.*gecko\/[0-9]+.*k-ninja\/([0-9a-z\+\-\.]+).*/si' => 'K-Ninja',
				'/mozilla.*rv[ :][0-9\.]+.*gecko\/[0-9]+.*firebird\/([0-9a-z\+\-\.]+).*/si' => 'Firebird (old name for Firefox)',
				'/mozilla.*rv:[0-9\.]+.*gecko\/[0-9]+.*phoenix\/([0-9a-z\+\-\.]+).*/si' => 'Phoenix (old name for Firefox)',
				'/mozilla.*konqueror\/([0-9a-z\+\-\.]+).*/si' => 'Konqueror',
				'/mozilla.*applewebkit\/[0-9]+.*omniweb\/v[0-9\.]+/si' => 'OmniWeb',
				'/mozilla.*applewebkit\/[0-9]+.*sunrisebrowser\/([0-9a-z\+\-\.]+)/si' => 'Sunrise',
				'/dillo\/([0-9a-z\+\-\.]+).*/si' => 'Dillo',
				'/icab[ \/]([0-9a-z\+\-\.]+).*/si' => 'iCab',
				'/^lynx\/([0-9a-z\.]+).*/si' => 'Lynx',
				'/^elinks \(([0-9a-z\.]+).*/si' => 'Elinks',
				'/^elinks\/([0-9a-z\.]+).*/si' => 'Elinks',
				'/^elinks$/si' => 'Elinks',
				'/Amiga\-Aweb\/([0-9a-z\+\-\.]+).*/si' => 'Amiga Aweb',
				'/AmigaVoyager\/([0-9a-z\+\-\.]+).*/si' => 'Amiga Voyager',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*AOL ([0-9a-z\+\-\.]+)/si' => 'AOL Explorer',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*America Online Browser ([0-9a-z\+\-\.]+)/si' => 'AOL Explorer',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*Avant Browser ([0-9a-z\+\-\.]+)/si' => 'Avant Browser',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*Crazy Browser ([0-9a-z \+\-\.]+)/si' => 'Crazy Browser',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*Deepnet Explorer ([0-9a-z\+\-\.]+)/si' => 'Deepnet Explorer',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*iRider ([0-9a-z\+\-\.]+)/si' => 'iRider',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*KKman([0-9a-z\+\-\.]+)/si' => 'KKman',
				'/mozilla.*MultiZilla ([0-9a-z\+\-\.]+).*/si' => 'MultiZilla',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*NetCaptor ([0-9a-z\+\-\.]+)/si' => 'NetCaptor',
				'/Netgem\/([0-9a-z\+\-\.]+).*/si' => 'NetBox',
				'/netsurf\/([0-9a-z\+\-\.]+).*/si' => 'NetSurf',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*Sleipnir\/([0-9a-z\+\-\.]+)/si' => 'Sleipnir',
				'/sunrise[ \/]([0-9a-z\+\-\.\/]+)/si' => 'Sunrise',
				'/mozilla.*galeon\/([0-9a-z\+\-\.]+).*/si' => 'Galeon',
				'/MSIE.*PhaseOut/si' => 'Phaseout',
				'/^Enigma browser$/si' => 'Enigma browser',
				'/amaya\/([0-9a-zA-Z\.\-+]+)/si' => 'Amaya',
				'/^Mozilla.*OmniWeb\/([1-9a-zA-z\.\-]+)/si' => 'OmniWeb',
				'/Mozilla.*OffByOne/si' => 'Off By One',
				'/w3m\/([0-9a-zA-z\-\+\.]+)/si' => 'w3m',
				'/ICEbrowser\/([0-9a-z_\.\-]+)/si' => 'ICE browser',
				'/ICE browser\/([0-9a-z_\.\-]+)/si' => 'ICE browser',
				'/HotJava\/([0-9a-zA-Z\.\- ]+)/si' => 'HotJava',
				'/Mozilla.*MSIE.*Hydra Browser/si' => 'Hydra Browser',
				'/^Mozilla\/(3\.0).*Sun\)$/si' => 'HotJava',
				'/^Mozilla.*AppleWebKit.*wKiosk/si' => 'wKiosk',
				'/NCSA_Mosaic\/([0-9a-z\+\-\.]+).*/si' => 'NCSA Mosaic',
				'/mozilla.*rv:[0-9\.]+.*gecko\/[0-9]+.*Shiretoko\/([0-9a-z\+\-\.]+).*/si' => 'Firefox (Shiretoko)',
				'/mozilla.*rv:[0-9\.]+.*gecko\/[0-9]+.*Minefield\/([0-9a-z\+\-\.]+).*/si' => 'Firefox (Minefield)',
				'/^links \(([0-9a-z\.]+).*/si' => 'Links',
				'/Netbox\/([0-9a-z\+\-\.]+).*/si' => 'NetBox',
				'/^Midori\/([0-9\.]+)/si' => 'Midori',
				'/mozilla.*Lunascape ([0-9a-z\+\-\.]+).*/si' => 'Lunascape',
				'/^OmniWeb\/([0-9a-z\.\-]+)/si' => 'OmniWeb',
				'/mozilla.*rv:[0-9\.]+.*gecko\/[0-9]+.*chimera\/([0-9a-z\+\-\.]+).*/si' => 'Camino',
				'/^mozilla\/.*MSIE [0-9\.]+.*TheWorld/si' => 'TheWorld Browser',
				'/mozilla.*rv:[0-9\.]+.*gecko.*BonEcho\/([0-9a-z\+\-\.]+).*/si' => 'Firefox (BonEcho)',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*GreenBrowser/si' => 'GreenBrowser',
				'/^Mozilla\/.*AppleWebKit.*QtWeb Internet Browser\/([0-9\.]+)/si' => 'QtWeb',
				'/^Mozilla.*RISC.*Oregano ([0-9\.]+)/si' => 'Oregano',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*Acoo Browser/si' => 'Acoo Browser',
				'/^Mozilla\/.*ABrowse ([0-9\.]+).*Syllable/si' => 'ABrowse',
				'/NCSA Mosaic\/([0-9a-z\+\-\.]+).*/si' => 'NCSA Mosaic',
				'/mozilla.*rv:[0-9\.]+.*gecko.*Blackbird\/([0-9a-z\+\-\.]+).*/si' => 'Blackbird',
				'/mozilla.*applewebkit.*DeskBrowse\/([0-9a-z\+\-\.]+).*/si' => 'DeskBrowse',
				'/^Mozilla\/.*MSIE.*http:\/\/www\.Abolimba\.de/si' => 'Abolimba',
				'/^Mozilla\/.*Gecko\/.*Beonex\/([0-9a-z\.\-]+)/si' => 'Beonex',
				'/^DocZilla\/([0-9\.]+).*Gecko\//si' => 'DocZilla',
				'/^retawq\/([0-9a-z\.]+).*\(text\)$/si' => 'retawq',
				'/Mozilla\/.*AppleWebKit.*Dooble/si' => 'Dooble',
				'/^Bunjalloo\/([0-9\.]+).*Nintendo/si' => 'Bunjalloo',
				'/mozilla.*rv:[0-9\.]+.*gecko\/[0-9]+.*Namoroka\/([0-9a-z\+\-\.]+).*/si' => 'Firefox (Namoroka)',
				'/mozilla.*applewebkit.*rekonq[\/]{0,1}([0-9a-z\.]+){0,1}.*/si' => 'Rekonq',
				'/^X\-Smiles\/([0-9a-z\.]+)/si' => 'X-Smiles',
				'/^Mozilla\/.*Origyn Web Browser/si' => 'OWB',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*Browzar/si' => 'Browzar',
				'/^edbrowse\/([0-9\.\-]+)/si' => 'Edbrowse',
				'/^Mozilla\/.*MSIE.*Multi\-Browser ([0-9\.]+).*www\.multibrowser\.de/si' => 'Multi-Browser XP',
				'/^Mozilla.*compatible.*NetPositive\/([0-9\.]+)/si' => 'NetPositive',
				'/^WorldWideweb \(NEXT\)$/si' => 'WorldWideWeb',
				'/^MyIBrow\/([0-9\.]+).*Windows/si' => 'My Internet Browser',
				'/^Mozilla.*MSIE.*TencentTraveler ([0-9\.]+)/si' => 'TT Explorer',
				'/mozilla.*rv:[0-9\.]+.*gecko.*Swiftweasel\/([0-9a-z\+\-\.]+).*/si' => 'Swiftweasel',
				'/mozilla.*rv:[0-9\.]+.*gecko.*Lorentz\/([0-9a-z\+\-\.]+).*/si' => 'Firefox (Lorentz)',
				'/^Mozilla.*MSIE.*SlimBrowser/si' => 'SlimBrowser',
				'/^Mozilla.*Gecko.*Conkeror\/([0-9\.]+)/si' => 'Conkeror',
				'/^Mozilla.*Gecko\/[0-9]+.*WebianShell\/([0-9a-z\.]+)/si' => 'Webian Shell',
				'/Mozilla.*Windows.* Sundance\/([0-9a-z\.]+)/si' => 'Sundance',
				'/Sundance.*Windows.*Version\/([0-9a-z\.]+)/si' => 'Sundance',
				'/Mozilla.*AppleWebKit.*Usejump\/([0-9a-z\.]+)/si' => 'Usejump',
				'/^Mozilla.*Linux\/SmartTV.*AppleWebKit.*WebBrowser.*SmartTV/si' => 'Maple browser',
				'/^Mozilla.*Charon.*Inferno/' => 'Charon',
				'/Mozilla.*compatible.*DPlus ([0-9\.]+)/si' => 'DPlus',
				'/^Mozilla.*MSIE.*Windows.*SaaYaa/si' => 'SaaYaa Explorer',
				'/^Mozilla.*Nintendo WiiU.*AppleWebKit.*NX.*NintendoBrowser\/([0-9\.]+)/' => 'Nintendo Browser',
				'/^Mozilla.*Nintendo 3DS/si' => '3DS Browser',
				'/^Mozilla.*AppleWebKit.*LG Browser\/([0-9\.]+).*NetCast/si' => 'LG Web Browser',
				'/^Mozilla.*DTV.*AppleWebKit.*Espial\/([0-9\.]+)/si' => 'Espial TV Browser',
				'/^Mozilla.*PlayStation 4.*AppleWebKit/si' => 'PS4 Web browser',
				'/Mozilla.*AppleWebKit.*Maxthon/si' => 'Maxthon',
				'/^Mozilla.*SmartHub.*Linux.*Maple2012/si ' => 'Maple browser',
				'/^Mozilla.*AppleWebKit.*otter\/([0-9a-z\-\.]+).*Safari/si' => 'Otter',
				'/^IBM WebExplorer \/v([0-9\.]+)/si' => 'IBM WebExplorer',
				'/^Mozilla.*AppleWebKit.*KHTML.*WebKitGTK.*luakit/si' => 'luakit',
				'/^Mozilla.* AppleWebKit.*QtCarBrowser.*Safari/si' => 'Tesla Car Browser',
				'/mozilla.*opera ([0-9][0-9a-z\+\-\.]+).*/si' => 'Opera',
				'/^Mozilla.*MSIE.*TencentTraveler/si' => 'TT Explorer',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*Avant Browser/si' => 'Avant Browser',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*Deepnet Explorer/si' => 'Deepnet Explorer',
				'/mozilla.*\/[0-9\.]+.*gecko.*firefox.*/si' => 'Firefox',
				'/mozilla.*MSIE [0-9a-z\+\-\.]+.*Maxthon/si' => 'Maxthon',
				'/mozilla.*rv:[0-9\.]+.*gecko.*GranParadiso\/([0-9a-z\+\-\.]+).*/si' => 'Firefox (GranParadiso)',
				'/^Mozilla.*ANTFresco\/([0-9\.]+)/si' => 'ANT Fresco',
				'/^opera\/([0-9a-z\+\-\.]+).*/si' => 'Opera',
				'/IBrowse\/([0-9a-z\+\-\.]+).*/si' => 'IBrowse',
				'/IBrowse/si' => 'IBrowse',
				'/Aweb.*Amiga/si' => 'Amiga Aweb',
				'/Lynx/si' => 'Lynx',
				'/^opera ([0-9a-z\+\-\.]+).*/si' => 'Opera',
				'/^Webkit\/.*Uzbl/si' => 'Uzbl',
				'/^Uzbl.*Webkit/si' => 'Uzbl',
				'/Mozilla\/4.*OS\/2/si' => 'Netscape Navigator',
				'/^Surf\/([0-9\.]+).*AppleWebKit/si' => 'Surf',
				'/mozilla.*(rv:[0-9\.]+).*gecko\/[0-9]+.*/si' => 'Mozilla',
				'/^Mozilla.*SkipStone ([0-9\.]+)/si' => 'SkipStone',
				'/mozilla.*applewebkit.*\/[0-9a-z\+\-\.]+.*version\/([0-9a-z\+\-\.]+).*safari\/[0-9a-z\+\-\.]+.*/si' => 'Safari',
				'/mozilla.*applewebkit.*\/[0-9a-z\+\-\.]+.*safari\/([0-9a-z\+\-\.]+).*/si' => 'Safari',
				'/^Mozilla\/([0-9\.]+).*Nav\)/si' => 'Netscape Navigator',
				'/mozilla.*(rv:[0-9\.]+).*/si' => 'Mozilla',
				'/^Avant Browser/si' => 'Avant Browser',
				'/^Mozilla\/4.0 \(compatible; MSIE ([0-9\.]+); Windows/si' => 'IE',
				'/mozilla\/.*MSIE ([0-9b\.]+).*/si' => 'IE',
			),
			'Email client' => array(
				'/^Mozilla.*Thunderbird\/([0-9a-zA-Z\.]+)/si' => 'Thunderbird',
				'/^Mozilla.*Shredder\/([0-9a-zA-Z\.]+)/si' => 'Shredder',
				'/^Mozilla.*OS X.*AppleWebKit.*KHTML.*Sparrow\/([0-9\.]+)/si' => 'Sparrow',
				'/^Mozilla.*MSIE.*MSOffice 12/si' => 'Outlook 2007',
				'/^Mozilla.*MSIE.*MSOffice 14/si' => 'Outlook 2010',
				'/^Microsoft Office\/14.*MSOffice 14/si' => 'Outlook 2010',
				'/^Microsoft Office\/14.*Microsoft Outlook 14/si' => 'Outlook 2010',
				'/^Mozilla.*MSIE.*Microsoft Outlook 15/si' => 'Outlook 2013',
				'/^Outlook-Express\/7\.0 \(MSIE 7\.0.*Windows/si' => 'Windows Live Mail',
				'/^Outlook-Express\/7\.0 \(MSIE 6\.0.*Windows/si' => 'Windows Live Mail',
				'/^Outlook-Express\/7\.0 \(MSIE 8.*Windows/si' => 'Windows Live Mail',
				'/^Outlook-Express\/7\.0 \(MSIE 9.*Windows/si' => 'Windows Live Mail',
				'/mozilla.*Lotus-Notes\/([0-9a-z\+\-\.]+).*/si' => 'Lotus Notes',
				'/mozilla.*rv:[0-9\.]+.*gecko.*Spicebird\/([0-9a-z\+\-\.]+).*/si' => 'Spicebird',
				'/^The Bat! ([0-9\.]+)$/si' => 'The Bat!',
				'/^Mozilla.*Postbox\/([0-9a-zA-Z\.]+)/si' => 'Postbox',
				'/^GcMail Browser\/([0-9\.]+)/si' => 'GcMail',
				'/^Pocomail\/([0-9\.]+)/si' => 'PocoMail',
				'/^PocoMail ([0-9\.]+)/si' => 'PocoMail',
				'/^The Bat! Voyager ([0-9\.]+)$/si' => 'The Bat!',
				'/^PocomailPE\/([0-9\.]+)/si' => 'PocoMail',
				'/^Eudora\/?([0-9a-z\.]+)*/si ' => 'Eudora',
				'/^Microsoft Office\/15.*Microsoft Outlook 15/si' => 'Outlook 2013',
				'/^Airmail ([0-9\.]+).*Mac OS X/si' => 'AirMail',
				'/^Airmail\/([0-9]+) CFNetwork\/[0-9\.]+/si' => 'AirMail',
				'/^Postbox ([0-9a-z\.]+)/si' => 'Postbox',
				'/^Barca\/([0-9\.]+)/si' => 'Barca',
				'/^BarcaPro\/([0-9\.]+)/si' => 'Barca',
				'/^Mozilla.*Mac.*AppleWebKit.*\(KHTML, like Gecko\)$/si' => 'Apple Mail',
			),
			'Other' => array(
				'/\/szn-mobile-transcoder/si' => 'Seznam WAP Proxy',
				'/Google Wireless Transcoder/si' => 'Google Wireless Transcoder',
				'/^Mozilla.*\(via docs\.google\.com\/viewer\)/si' => 'Google Docs viewer',
				'/mozilla.*rv:[0-9\.]+.*gecko.*Prism\/([0-9a-z\+\-\.]+).*/si' => 'Prism',
				'/^Mozilla\/.*Mobile Content Viewer\/([0-9\.]+).*NetFront/si' => 'NetFront Mobile Content Viewer',
				'/^Mozilla\/5\.0.*\(via ggpht\.com.*\)$/si' => 'Gmail image proxy',
				'/^webfs\/([0-9\.]+) \(plan 9\)$/si' => 'webfs',
				'/Mozilla\/.*AppleWebKit.*Paparazzi!\/([0-9a-z\.]+)/si' => 'Paparazzi!',
				'/Bookdog\/([0-9\.]+)/si' => 'Bookdog',
				'/^gPodder\/([0-9\.]+)/si' => 'gPodder',
				'/^PHP\/([0-9a-z\.\-]+)$/si' => 'PHP',
				'/^GoldenPod ([0-9\.]+)/si' => 'GoldenPod',
				'/^Mozilla\/4\.0.*Win32.*ActiveXperts\.Http\.([0-9\.]+)/si' => 'ActiveXperts Network Monitor',
				'/^check_http\/([0-9a-z\.]+) \(nagios\-plugins/si' => 'HTTP nagios plugin',
				'/^GSiteCrawler\/([0-9a-z\.]+)/si' => 'GSiteCrawler',
				'/^lftp\/([0-9a-z\.]+)$/s' => 'LFTP',
				'/^LinkbackPlugin\/([0-9a-z\.]+) Laconica\//si' => 'LinkbackPlugin for Laconica',
				'/^Microsoft Data Access Internet Publishing Provider DAV/si' => 'Microsoft WebDAV client',
				'/^gvfs\/([0-9a-z\.]+)/si' => 'GnomeVFS',
				'/^Funambol Outlook Plug-in.*([0-9\.]+)$/si' => 'Funambol Outlook Sync Client',
				'/^Funambol Mozilla Sync Client v([0-9\.]+)$/si' => 'Funambol Mozilla Sync Client',
				'/^TulipChain\/([0-9\.]+).*ostermiller.org\/tulipchain.*Java/si' => 'Tulip Chain',
				'/^SZN-Image-Resizer$/si' => 'Seznam WAP Proxy',
				'/^Google-Listen\/([0-9a-z\.]+)/si' => 'Google Listen',
				'/^Claws Mail GtkHtml2 plugin ([0-9a-z\.]+).*http:\/\/www.claws-mail.org\/plugins.php/si' => 'Claws Mail GtkHtml2 plugin',
				'/^JoeDog\/.*Siege ([0-9\.]+)/si' => 'Siege',
				'/^ApacheBench\/([0-9a-z\-\.]+)$/si' => 'AB (Apache Bench)',
				'/^muCommander v([0-9\.]+)/si' => 'muCommander',
				'/^Radio Downloader ([0-9\.]+)$/si' => 'Radio Downloader',
				'/^Cyberduck\/([0-9\.]+)/si' => 'Cyberduck',
				'/^Win.*Jamcast\/([0-9\.]+)/si' => 'Jamcast',
				'/^Jamcast ([0-9\.]+)$/si' => 'Jamcast',
				'/^Mozilla.*compatible.*BorderManager ([0-9\.]+)/si' => 'Novell BorderManager',
				'/^mozilla.*AppleWebKit.*Google Earth\/([0-9\.]+)/si ' => 'Google Earth',
				'/^LeechCraft.*LeechCraft\/Poshuku ([0-9a-z\-\.]+)/si' => 'LeechCraft',
				'/^Web-sniffer\/([0-9\.]+).*web-sniffer\.net\/\)$/si' => 'Web-sniffer',
				'/^Atomic_Email_Hunter\/([0-9\.]+)$/si' => 'Atomic Email Hunter',
				'/^Microsoft Office Existence Discovery/si' => 'Microsoft Office Existence Discovery',
				'/^BrownReclusePro v([0-9\.]+).*SoftByteLabs.com/si' => 'BrownRecluse',
				'/Nokia SyncML HTTP Client/si' => 'Nokia SyncML Client',
				'/^JS\-Kit URL Resolver.*js-kit\.com/si' => 'JS-Kit/Echo',
				'/^Podkicker\/([0-9\.]+)/si' => 'Podkicker',
				'/^Podkicker Pro\/([0-9\.]+)/si' => 'Podkicker',
				'/^WordPress\/[0-9\.]+; http:\/\//si' => 'WordPress pingback',
				'/A1 Sitemap Generator\/([0-9\.]+).*microsystools.com/si' => 'A1 Sitemap Generator',
				'/Pattern\/([0-9\.]+).*.clips\.ua\.ac\.be\/pages\/pattern/si' => 'Pattern',
				'/^BrowserEmulator\/0\.9 see http:\/\/dejavu\.org/si' => 'BrowserEmulator',
				'/^Mozilla.*AppleWebKit.*PhantomJS\/([0-9\.]+) Safari/si' => 'PhantomJS',
				'/^Mozilla.*compatible; HTTP SmartBrowserPlugin ([0-9\.]+) for TotalCommander/si' => 'SmartBrowserPlugin for TC',
				'/^FeedBurner\/([0-9\.]+).*www\.FeedBurner\.com/si' => 'FeedBurner',
				'/^YOURLS v([0-9\.]+).*yourls.org.*running on/si' => 'YOURLS',
				'/^Mozilla\/5.0 \(compatible; Yahoo Link Preview; https:\/\/help\.yahoo\.com\/kb\/mail\/yahoo-link-preview-SLN23615\.html\)/si' => 'Yahoo Link Preview',
				'/http:\/\/code\.google\.com\/appengine/si' => 'Google App Engine',
				'/^iGooMap\/([0-9a-z\.]+).*pointworks/si' => 'iGooMap',
				'/^ColdFusion \(BookmarkTracker\.com\)$/si' => 'BookmarkTracker',
				'/^WhatWeb\/([0-9a-z\.\-]+)/si' => 'WhatWeb',
				'/^webcollage\/([0-9\.]+)$/si' => 'WebCollage',
				'/^webcollage\-noporn\/([0-9\.]+)$/si' => 'WebCollage',
				'/^webcollage\.[a-z]+\/([0-9\.]+)$/si' => 'WebCollage',
				'/^webcollage1\/([0-9\.]+)$/si' => 'WebCollage',
				'/^iVideo ([a-z0-9\.\ ]+).*iPhone OS/si' => 'iVideo',
				'/^Mozilla\/4\.0 \(compatible; Synapse\)$/si' => 'Apache Synapse',
				'/^Mozilla\/.*compatible.*Powermarks\/([0-9\.]+)/si' => 'Powermarks',
				'/^Googlebot-richsnippets/si' => 'Google Rich Snippets Testing Tool',
				'/^Mozilla.*PRTG Network Monitor/si' => 'PRTG Network Monitor',
				'/^DownloadStudio\/([0-9\.]+)$/si' => 'DownloadStudio',
				'/^WinPodder.*http:\/\/winpodder\.com/si' => 'WinPodder',
				'/^Azureus ([0-9a-z\.]+)/si' => 'Vuze',
				'/^muCommander-file-API/si' => 'muCommander',
				'/^LeechCraft/si' => 'LeechCraft',
				'/^iGetter\/([0-9a-z\.]+).*/si' => 'iGetter',
				'/^GoogleFriendConnect\/([0-9\.]+)$/si' => 'Google Friend Connect',
				'/^HTML2JPG.*http:\/\/www.html2jpg.com/si' => 'HTML2JPG',
				'/^Apache.*internal dummy connection/si' => 'Apache internal dummy connection',
				'/^DellWebMonitor\/([0-9\.]+)/' => 'Dell Web Monitor',
				'/^holmes\/([0-9\.]+)/si' => 'Holmes',
				'/^Mozilla.*sp_auditbot\/([0-9\.]+).*www\.seoprofiler\.com\/auditbot/si' => 'sp_auditbot', // Ajouté 2015-06-20
			),
			'Validator' => array(
				'/^W3C-checklink\/([0-9a-z\+\-\.]+).*/si' => 'W3C Checklink',
				'/^2Bone_LinkChecker\/([0-9\.]+)/si' => '2Bone LinkChecker',
				'/^Xenu Link Sleuth ([0-9a-z\+\-\.]+)$/si' => 'Xenu',
				'/^W3C_Validator\/([0-9a-z\+\-\.]+).*/si' => 'W3C Validator',
				'/^HTMLParser\/([0-9a-z\.]+)$/si' => 'HTMLParser',
				'/^WDG_Validator\/([0-9\.]+)/si' => 'WDG Validator',
				'/^CSSCheck\/([0-9\.]+)/si' => 'WDG CSSCheck',
				'/^Page Valet\/([0-9a-z\.]+)/si' => 'WDG Page Valet',
				'/^FeedValidator\/([0-9\.]+)$/si' => 'FeedValidator',
				'/^REL Link Checker.*([0-9\.]+)/si' => 'REL Link Checker Lite',
				'/^Jigsaw\/[0-9\.]+ W3C_CSS_Validator_JFouffa\/([0-9\.]+)/si' => 'W3C CSS Validator',
				'/^FPLinkChecker\/([0-9\.]+)$/si' => 'PHP link checker',
				'/^P3P Validator$/si' => 'P3P Validator',
				'/^Cynthia ([0-9\.]+)$/si' => 'Cynthia',
				'/^LinkExaminer\/([0-9\.]+) \(Windows\)$/si' => 'LinkExaminer',
				'/^LinkChecker\/([0-9\.]+).*linkchecker\.sourceforge\.net/si' => 'LinkChecker',
				'/^W3C_Multipage_Validator\/([0-9a-z\.]+).*http:\/\/www\.validator\.ca\//si' => 'Multipage Validator',
				'/^W3C-mobileOK\/DDC-([0-9\.]+).* http:\/\/www.w3.org\/2006\/07\/mobileok-ddc/si' => 'W3C mobileOK Checker',
				'/^anw webtool LoadControl\/([0-9\.]+)$/si' => 'anw LoadControl',
				'/^topSUBMIT.de HTMLChecker\/([0-9\.]+)$/si' => 'anw HTMLChecker',
				'/^LinkWalker\/([0-9\.]+).*www\.seventwentyfour\.com/si' => 'LinkWalker',
				'/^Checkbot\/([0-9\.]+)/si' => 'Checkbot',
				'/^Validator.nu\/([0-9\.]+)$/si' => 'Validator.nu',
				'/^Xenu Link Sleuth\/([0-9a-z\+\-\.]+)$/si' => 'Xenu',
				'/^Screaming Frog SEO Spider\/([0-9\.]+)/si' => 'Screaming Frog SEO Spider',
				'/^A1 Website Analyzer\/([0-9\.]+).*www.microsystools.com\/products\/website-analyzer.*miggibot/si' => 'A1 Website Analyzer',
				'/^Mozilla.*compatible.*LinkChecker\/([0-9\.]+).*wummel.github.io\/linkchecker/si' => 'LinkChecker',
				'/^Validator.nu\/LV$/si' => 'Validator.nu',
				'/^CSE HTML Validator.* Online/si' => 'CSE HTML Validator',
			),
			'Feed Reader' => array(
				'/^Mozilla\/.*AppleWebKit.*NetNewsWire\/([0-9a-z\.]+)$/si' => 'NetNewsWire',
				'/^CPG RSS Module File Reader/si' => 'CPG Dragonfly RSS Module',
				'/^Bloglines\/([0-9\.]+)/si' => 'Bloglines',
				'/^Gregarius\/([0-9\.]+)/si' => 'Gregarius',
				'/^Apple-PubSub\/([0-9\.]+)$/si' => 'Apple-PubSub',
				'/Feedfetcher-Google.*http:\/\/www\.google\.com\/feedfetcher\.html/si' => 'Feedfetcher-Google',
				'/^BlogBridge ([0-9\.]+)/si' => 'BlogBridge',
				'/^Liferea\/([0-9\.]+).*http:\/\/liferea\.sf\.net\//si' => 'Liferea',
				'/^HomePage Rss Reader ([0-9\.]+)/si' => 'Seznam RSS reader',
				'/^Dragonfly File Reader/si' => 'CPG Dragonfly RSS Module',
				'/^CPG Dragonfly RSS Module Feed Viewer/si' => 'CPG Dragonfly RSS Module',
				'/^newsbeuter\/([0-9\.]+)/si' => 'Newsbeuter',
				'/^JetBrains Omea Reader ([0-9\.]+)/si' => 'Omea Reader',
				'/^YahooFeedSeeker\/([0-9\.]+)/si' => 'YahooFeedSeeker',
				'/^NewsGatorOnline\/([0-9\.]+) \(http:\/\/www\.newsgator\.com/si' => 'NewsGatorOnline',
				'/^Awasu\/([0-9a-z\.]+)$/si' => 'Awasu',
				'/^NetNewsWire\/([0-9a-z\.]+).*http:\/\/www\.newsgator\.com\/Individuals\/NetNews/si' => 'NetNewsWire',
				'/^Mozilla.*NewsFox\/([0-9\.]+)/si' => 'NewsFox',
				'/^Ilium Software NewsBreak/si' => 'NewsBreak',
				'/^GreatNews\/([0-9\.]+)$/si' => 'GreatNews',
				'/^Mozilla\/4.0 \(compatible; RSS Popper\)$/si' => 'RSS Popper',
				'/^RssBandit\/([0-9\.]+)/si' => 'Rss Bandit',
				'/^Fastladder FeedFetcher\/([0-9\.]+).*fastladder.com/si' => 'Fastladder FeedFetcher',
				'/^Akregator\/([0-9\.]+).*librss\/remnants/si' => 'Akregator',
				'/^AppleSyndication\/([0-9\.]+)$/si' => 'Safari RSS reader',
				'/^Windows-RSS-Platform\/([0-9\.]+).*MSIE.* Windows/si' => 'IE RSS reader',
				'/^Trileet NewsRoom.*feedmonger\.blogspot\.com/si' => 'Trileet NewsRoom',
				'/^Feed Viewer ([0-9\.]+)$/si' => 'Feed Viewer',
				'/^iCatcher! ([0-9\.]+).*iPhone OS/si' => 'iCatcher!',
				'/^Reeder\/([0-9\.]+).*CFNetwork/si' => 'Reeder',
				'/^FeedDemon\/([0-9\.]+).*(www\.feeddemon\.com|www\.newsgator\.com)/si' => 'FeedDemon',
				'/Y!.*yahoo.*YahooFeedSeekerBetaJp\/([0-9\.]+)/si' => 'YahooFeedSeeker',
				'/^Mozilla\/5\.0 \(Sage\)$/si' => 'Sage',
				'/^Netvibes.*http:\/\/www\.netvibes\.com/si' => 'Netvibes feed reader',
				'/^InoReader.*inoreader\.com/si' => 'InoReader',
				'/^Mozilla.*inoreader\.com/si' => 'InoReader',
				'/^Abilon$/si' => 'Abilon',
				'/^RSS Menu\/([0-9\.]+)/si' => 'RSS Menu',
				'/^RSSOwl\/([0-9]\.[0-9]\.[0-9])/si' => 'RSSOwl',
				'/^NFReader\/([0-9\.]+)/si' => 'NFReader',
				'/^SharpReader\/([0-9\.]+)/si' => 'SharpReader',
				'/^YeahReader/si' => 'YeahReader',
			),
			'Library' => array(
				'/^xine\/([0-9a-z\.]+)/si' => 'xine',
				'/libwww\-perl\/([0-9a-z\+\-\.]+).*/si' => 'libwww-perl',
				'/lwp\-request\/([0-9a-z\+\-\.]+).*/si' => 'libwww-perl',
				'/Jakarta Commons-HttpClient\/([0-9a-zA-Z\.\-]+)/si' => 'Jakarta Commons-HttpClient',
				'/^curl ([0-9a-zA-Z\.\-]+)/si' => 'cURL',
				'/Python\-urllib\/([0-9a-zA-Z\.\-]+)/si' => 'Python-urllib',
				'/Jakarta Commons\-HttpClient/si' => 'Jakarta Commons-HttpClient',
				'/^Python-webchecker\/([0-9]+)$/si' => 'Python-webchecker',
				'/poe-component-client-http\/([0-9a-z\.\-]+)/si' => 'POE-Component-Client-HTTP',
				'/snoopy v([1-9\.]+)/si' => 'Snoopy',
				'/mozilla.*applewebkit.*AdobeAIR\/([0-9a-z\+\-\.]+).*/si' => 'Adobe AIR runtime',
				'/^lwp-trivial\/([0-9.]+)$/si' => 'LWP::Simple',
				'/^WWW-Mechanize\/([0-9a-z\+\-\.]+)/si' => 'WWW::Mechanize',
				'/^LWP::Simple\/([0-9a-z\.]+)$/si' => 'LWP::Simple',
				'/^Java\/([0-9a-z\._\-]+)/si' => 'Java',
				'/^UniversalFeedParser\/([0-9\.]+)/si' => 'FeedParser',
				'/^XML-RPC for PHP ([0-9a-z\.]+)$/si' => 'XML-RPC for PHP',
				'/^SimplePie\/([0-9a-z\. ]+)/si' => 'SimplePie',
				'/^PycURL\/([0-9\.]+)$/si' => 'PycURL',
				'/^MagpieRSS\/([0-9\.]+)/si' => 'MagpieRSS',
				'/^curl\/([0-9a-zA-Z\.\-]+)/si' => 'cURL',
				'/^Java([0-9\._]+)$/si' => 'Java',
				'/^Feed::Find\/([0-9\.]+)$/si' => 'Feed::Find',
				'/^libsoup\/([0-9a-z\.]+)$/si' => 'LibSoup',
				'/^libsummer\/([0-9\.]+)/si' => 'Summer',
				'/^GStreamer souphttpsrc libsoup\/[0-9\.]+$/si' => 'GStreamer',
				'/^php-openid\/([0-9\.]+)/si' => 'PHP OpenID library',
				'/urlgrabber\/([0-9\.]+)/si' => 'urlgrabber',
				'/Python\-urllib$/si' => 'Python-urllib',
				'/^Mozilla\/3.0 \(compatible; Indy Library\)$/si' => 'Indy Library',
				'/^Rome Client \(http:\/\/tinyurl\.com\/64t5n\) Ver: ([0-9\.]+)/si' => 'ROME library',
				'/^HTTP_Request2\/([0-9\.]+)/si' => 'HTTP_Request2',
				'/^Zend_Http_Client$/si' => 'Zend_Http_Client',
				'/^CamelHttpStream\/([0-9\.]+)/si' => 'Evolution/Camel.Stream',
				'/^EventMachine HttpClient/si' => 'EventMachine',
				'/^python-requests\/([0-9\.]+)/si' => 'Python-requests',
				'/^PEAR HTTP_Request class \( http:\/\/pear.php.net\/ \)/si' => 'PEAR HTTP_Request',
				'/^Mechanize\/([0-9\.]+).*Ruby.*github.com\/(tenderlove|sparklemotion)\/mechanize/si' => 'Mechanize',
				'/^htmlayout ([0-9\.]+).*Win.*www\.terrainformatica\.com/si' => 'HTMLayout',
				'/^Anemone\/([0-9\.]+)$/si' => 'Anemone',
				'/^Apache-HttpClient\/([0-9\.]+)/si' => 'Apache-HttpClient',
				'/^XMLRPC::Client \(Ruby ([0-9\.]+)\)$/si' => 'XML-RPC for Ruby',
				'/^RestSharp ([0-9\.]+)$/si' => 'RestSharp',
				'/^Go ([0-9\.]+) package http/si' => 'Go http package',
				'/^Apache-HttpAsyncClient\/([0-9a-z\.]+)/si' => 'Apache-HttpAsyncClient',
				'/^Dalvik\/[0-9\.].*Linux.*Android/si' => 'Android HttpURLConnection',
				'/libwww\-perl/si' => 'libwww-perl',
				'/^PHPCrawl$/si' => 'PHPcrawl',
				'/^The Incutio XML-RPC PHP Library/si' => 'IXR lib',
				'/^BinGet\/([0-9a-zA-Z\.]+)/si' => 'BinGet',
				'/^CFNetwork\/([0-9\.]+)/si' => 'CFNetwork',
				'/^Guzzle\/([0-9\.]+) curl.*PHP/si' => 'Guzzle',
				'/^eat\/([0-9\.]+).*ruby/si' => 'Ruby eat',
				'/^Manticore ([0-9\.]+)/si' => 'Manticore',
				'/^Typhoeus.*http:\/\/github.com\/pauldix\/typhoeus/si' => 'Typhoeus',
				'/^Chilkat\/([0-9\.]+) \(\+http:\/\/www\.chilkatsoft\.com\/ChilkatHttpUA\.asp\)/si' => 'Chilkat HTTP .NET',
				'/^Typhoeus.*http:\/\/github.com\/dbalatero\/typhoeus/si' => 'Typhoeus',
				'/^Typhoeus.*https:\/\/github.com\/typhoeus\/typhoeus/si' => 'Typhoeus',
				'/^Go http package$/si' => 'Go http package',
				'/WinHttp/si' => 'WinHTTP',
			),
			'Offline Browser' => array(
				'/^Wget\/([0-9a-z\+\-\.]+).*/si' => 'Wget',
				'/offline explorer\/([0-9a-z\+\-\.]+).*/si' => 'Offline Explorer',
				'/mozilla.*AvantGo ([0-9a-z\+\-\.]+)/si' => 'AvantGo',
				'/mozilla.*HTTrack ([0-9a-z\+\-\.]+).*/si' => 'HTTrack',
				'/.*isilox\/([0-9a-z\+\-\.]+).*/si' => 'iSiloX',
				'/Teleport Pro\/([0-9a-z\+\-\.]+).*/si' => 'Teleport Pro',
				'/webcopier.*v([0-9a-z\.]+)/si' => 'WebCopier',
				'/GetRight\/([0-9a-zA-Z\.\-\+]+)/si' => 'GetRight',
				'/^WebZIP\/([0-9a-zA-Z\.\-]+)/si' => 'WebZIP',
				'/JoBo\/([0-9a-z\.\-]+)/si' => 'JoBo',
				'/^SiteSucker\/([0-9a-z\.]+)/si' => 'SiteSucker',
				'/^Web Downloader\/([0-9\.]+)$/si' => 'Offline Explorer',
				'/^iSiloXC\/([0-9\.]+)/si' => 'iSiloXC',
				'/^WebStripper\/([0-9\.]+)/si' => 'WebStripper',
				'/^webfetch\/([0-9\.]+)/si' => 'webfetch',
				'/^BlueCrab\/([0-9]+).*OS X/si' => 'BlueCrab',
				'/^A1 Website Download\/([0-9\.]+).*www.microsystools.com\/products\/website-download.*miggibot/si' => 'A1 Website Download',
				'/^CyotekWebCopy\/([0-9\.]+) CyotekWebCrawler/si' => 'Cyotek WebCopy',
				'/^Xaldon_WebSpider\/([0-9a-z\.]+)/si' => 'Xaldon WebSpider',
				'/^Xaldon WebSpider ([0-9a-z\.]+)/si' => 'Xaldon WebSpider',
				'/^Axel ([0-9\.]+)/si' => 'Axel',
				'/^SuperBot\/([0-9\.]+)/si' => 'SuperBot',
			),
			'Wap Browser' => array(
				'/^klondike\/([0-9a-z\+\-\.]+).*/si' => 'Klondike',
				'/^WapTiger\/5.0 \(http:\/\/www\.waptiger\.com\/.*/si' => 'WapTiger',
				'/^WinWAP\/([0-9\.]+)/si' => 'WinWap',
				'/^WinWAP-SPBE\/([0-9\.]+)/si' => 'WinWap',
			),
			'Useragent Anonymizer' => array(
				'/http:\/\/Anonymouse.org\/ \(Unix\)/si' => 'Anonymouse.org',
			),
		);

		/**
		 * Tableau de navigateurs en fonction de leur plate-forme, regroupés par familles
		 * 
		 * @var array
		 */
		private static $browserPlatforms = array(
			'Mac OS' => array(
				'OmniWeb' => 'Mac OS',
				'SiteSucker' => 'Mac OS',
				'Bookdog' => 'Mac OS',
				'Apple-PubSub' => 'Mac OS',
				'Cyberduck' => 'Mac OS',
			),
			'Linux' => array(
				'Dillo' => 'Linux',
				'LibSoup' => 'Linux',
				'xine' => 'Linux',
				'GnomeVFS' => 'Linux',
				'MicroB' => 'Linux (Maemo)',
				'Tear' => 'Linux (Maemo)',
				'Akregator' => 'Linux',
				'Gmail image proxy' => 'Linux',
			),
			'Windows' => array(
				'Offline Explorer' => 'Windows',
				'Sleipnir' => 'Windows',
				'Teleport Pro' => 'Windows',
				'WebCopier' => 'Windows',
				'Enigma browser' => 'Windows',
				'GetRight' => 'Windows',
				'WebZIP' => 'Windows',
				'Xenu' => 'Windows',
				'REL Link Checker Lite' => 'Windows',
				'Abilon' => 'Windows',
				'Windows Media Player' => 'Windows',
				'Omea Reader' => 'Windows',
				'GSiteCrawler' => 'Windows',
				'RSS Radio' => 'Windows',
				'Awasu' => 'Windows',
				'Microsoft WebDAV client' => 'Windows',
				'Funambol Outlook Sync Client' => 'Windows',
				'foobar2000' => 'Windows',
				'GreatNews' => 'Windows',
				'DownloadStudio' => 'Windows',
				'WinPodder' => 'Windows',
				'WinHTTP' => 'Windows',
				'Xaldon WebSpider' => 'Windows',
				'GOM Player' => 'Windows',
				'XMPlay' => 'Windows',
				'NFReader' => 'Windows',
				'Radio Downloader' => 'Windows',
				'WebStripper' => 'Windows',
				'RSS Popper' => 'Windows',
				'The Bat!' => 'Windows',
				'Rss Bandit' => 'Windows',
				'YeahReader' => 'Windows',
				'PocoMail' => 'Windows',
				'Atomic Email Hunter' => 'Windows',
				'webfetch' => 'Windows',
				'Powermarks' => 'Windows',
				'Feed Viewer' => 'Windows',
				'Microsoft Office Existence Discovery' => 'Windows',
				'BrownRecluse' => 'Windows',
				'HTML2JPG' => 'Windows',
				'Barca' => 'Windows',
				'A1 Sitemap Generator' => 'Windows',
				'SmartBrowserPlugin for TC' => 'Windows',
				'A1 Website Download' => 'Windows',
				'A1 Website Analyzer' => 'Windows',
				'Cyotek WebCopy' => 'Windows',
			),
			'Palm OS' => array(
				'Blazer' => 'Palm OS',
			),
			'JVM' => array(
				'JoBo' => 'JVM (Java)',
				'SEMC Browser' => 'JVM (Java)',
				'Polaris' => 'JVM (Platform Micro Edition)',
				'TeaShark' => 'JVM (Platform Micro Edition)',
				'X-Smiles' => 'JVM (Java)',
			),
			'Android' => array(
				'Google Listen' => 'Android',
			),
			'BeOS' => array(
				'NetPositive' => 'BeOS',
			),
			'unknown' => array(
				'Skyfire' => 'unknown',
			),
			'OS X' => array(
				'iGooMap' => 'OS X',
				'CFNetwork' => 'OS X',
			),
			'Symbian OS' => array(
				'Nokia SyncML Client' => 'Symbian OS',
			),
			'OS/2' => array(
				'IBM WebExplorer' => 'OS/2',
			),
		);

		/**
		 * Tableau d'agents utilisateurs de robots en fonction de leur nom, regroupés par familles
		 * 
		 * @var array
		 */
		private static $robots = array(
			' Scrubby' => array(
				'Mozilla/5.0 (compatible; Scrubby/3.1; +http://www.scrubtheweb.com/help/technology.html)' => ' Scrubby/3.1',
				'Mozilla/5.0 (compatible; Scrubby/3.2; +http://seotools.scrubtheweb.com/webpage-analyzer.html)' => ' Scrubby/3.2',
				'Scrubby/3.0 (+http://www.scrubtheweb.com/help/technology.html)' => ' Scrubby/3.0',
			),
			'007AC9' => array(
				'Mozilla/5.0 (compatible; 007ac9 Crawler; http://crawler.007ac9.net/)' => '007AC9',
			),
			'008' => array(
				'Mozilla/5.0 (compatible; 80bot/0.71; http://www.80legs.com/spider.html;) Gecko/2008032620' => '008/0.71',
				'Mozilla/5.0 (compatible; 008/0.83; http://www.80legs.com/spider.html;) Gecko/2008032620' => '008/0.83',
				'Mozilla/5.0 (compatible; 008/0.83; http://www.80legs.com/webcrawler.html;) Gecko/2008032620' => '008/0.83',
				'Mozilla/5.0 (compatible; 008/0.85; http://www.80legs.com/webcrawler.html) Gecko/2008032620' => '008/0.85',
			),
			'192.comAgent' => array(
				'192.comAgent' => '192.comAgent',
			),
			'200PleaseBot' => array(
				'Mozilla/5.0 (compatible; 200PleaseBot/1.0; +http://www.200please.com/bot)' => '200PleaseBot/1.0',
			),
			'360Spider' => array(
				'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.8.0.11) Gecko/20070312 Firefox/1.5.0.11; 360Spider' => '360Spider',
				'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.8.0.11)  Firefox/1.5.0.11; 360Spider' => '360Spider',
				'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; )  Firefox/1.5.0.11; 360Spider' => '360Spider',
			),
			'4seohuntBot' => array(
				'Mozilla/5.0 (compatible; 4SeoHuntBot; +http://4seohunt.biz/about.html)' => '4seohuntBot',
			),
			'50.nu' => array(
				'50.nu/0.01 ( +http://50.nu/bot.html )' => '50.nu/0.01',
			),
			'A6-Indexer' => array(
				'A6-Indexer/1.0 (http://www.a6corp.com/a6-web-scraping-policy/)' => 'A6-Indexer/1.0',
			),
			'abby' => array(
				'Mozilla/5.0 (compatible; abby/1.0; +http://www.ellerdale.com/crawler.html)' => 'abby/1.0',
			),
			'Aboundexbot' => array(
				'Aboundex/0.2 (http://www.aboundex.com/crawler/)' => 'Aboundexbot/0.2',
				'Aboundex/0.3 (http://www.aboundex.com/crawler/)' => 'Aboundexbot/0.3',
			),
			'AboutUsBot' => array(
				'AboutUsBot' => 'AboutUsBot',
				'Mozilla/5.0 (compatible; AboutUsBot/0.9; +http://www.aboutus.org/AboutUsBot)' => 'AboutUsBot/0.9',
				'Mozilla/5.0 (compatible; AboutUsBot Johnny5/2.0; +http://www.AboutUs.org/)' => 'AboutUsBot Johnny5/2.0',
				'AboutUsBot/Harpy (Website Analysis; http://www.aboutus.org/Aboutus:Bot; help@aboutus.org)' => 'AboutUsBot/Harpy',
			),
			'Abrave Spider' => array(
				'Abrave Spider v4 Robot 1 (http://robot.abrave.co.uk)' => 'Abrave Spider/4-1',
				'Abrave Spider v4 Robot 2 (http://robot.abrave.co.uk)' => 'Abrave Spider/4-2',
			),
			'Accelobot' => array(
				'Mozilla/5.0 (compatible; heritrix/1.12.0 +http://www.accelobot.com)' => 'Accelobot',
				'Accelobot' => 'Accelobot',
				'Mozilla/5.0 (compatible; heritrix/1.14.3 +http://www.accelobot.com)' => 'Accelobot',
			),
			'Accoona-AI-Agent' => array(
				'Accoona-AI-Agent/1.1.1 (crawler at accoona dot com)' => 'Accoona-AI-Agent/1.1.1',
				'Accoona-AI-Agent/1.1.2 (aicrawler at accoonabot dot com)' => 'Accoona-AI-Agent/1.1.2',
			),
			'AcoonBot' => array(
				'Acoon-Robot 4.0.0RC2 (http://www.acoon.de)' => 'Acoon-Robot 4.0.0RC2',
				'Acoon-Robot 4.0.1 (http://www.acoon.de)' => 'Acoon-Robot 4.0.1',
				'Acoon-Robot 4.0.2 (http://www.acoon.de)' => 'Acoon-Robot 4.0.2',
				'Acoon-Robot 4.0.2.17 (http://www.acoon.de)' => 'Acoon-Robot 4.0.2.17',
				'OpenAcoon v4.1.0 (www.openacoon.de)' => 'OpenAcoon v4.1.0',
				'Acoon v4.1.0 (www.acoon.de)' => 'Acoon v4.1.0',
				'Acoon v4.9.5 (www.acoon.de)' => 'Acoon v4.9.5',
				'Acoon v4.10.1 (www.acoon.de)' => 'Acoon v4.10.1',
				'Acoon v4.10.3 (www.acoon.de)' => 'Acoon v4.10.3',
				'Acoon v4.10.4 (www.acoon.de)' => 'Acoon v4.10.4',
				'Acoon v4.10.5 (www.acoon.de)' => 'Acoon v4.10.5',
				'OpenAcoon v4.10.5 (www.openacoon.de)' => 'OpenAcoon v4.10.5',
				'AcoonBot/4.10.5 (+http://www.acoon.de)' => 'AcoonBot/4.10.5',
				'Mozilla/5.0 (compatible; AcoonBot/4.10.6; +http://www.acoon.de/robot.asp)' => 'AcoonBot/4.10.6',
				'Mozilla/5.0 (compatible; AcoonBot/4.10.7; +http://www.acoon.de/robot.asp)' => 'AcoonBot/4.10.7',
				'Mozilla/5.0 (compatible; AcoonBot/4.10.8; +http://www.acoon.de/robot.asp)' => 'AcoonBot/4.10.8',
				'Mozilla/5.0 (compatible; AcoonBot/4.11.0; +http://www.acoon.de/robot.asp)' => 'AcoonBot/4.11.0',
				'Mozilla/5.0 (compatible; AcoonBot/4.11.1; +http://www.acoon.de/robot.asp)' => 'AcoonBot/4.11.1',
				'Mozilla/5.0 (compatible; AcoonBot/4.12.1; +http://www.acoon.de/robot.asp)' => 'AcoonBot/4.12.1',
			),
			'Acorn' => array(
				'Acorn/Nutch-0.9 (Non-Profit Search Engine; acorn.isara.org; acorn at isara dot org)' => 'Acorn/Nutch-0.9',
			),
			'AddThis.com' => array(
				'AddThis.com robot tech.support@clearspring.com' => 'AddThis.com robot',
			),
			'ADmantX Platform Semantic Analyzer' => array(
				'ADmantX Platform Semantic Analyzer - ADmantX Inc. - www.admantx.com - support@admantx.com' => 'ADmantX Platform Semantic Analyzer',
			),
			'adressendeutschland.de' => array(
				'www.adressendeutschland.de' => 'adressendeutschland.de',
			),
			'AdsBot-Google' => array(
				'AdsBot-Google (+http://www.google.com/adsbot.html)' => 'AdsBot-Google',
				'AdsBot-Google' => 'AdsBot-Google b',
			),
			'AhrefsBot' => array(
				'Mozilla/5.0 (compatible; AhrefsBot/1.0; +http://ahrefs.com/robot/)' => 'AhrefsBot/1.0',
				'Mozilla/5.0 (compatible; AhrefsBot/2.0; +http://ahrefs.com/robot/)' => 'AhrefsBot/2.0',
				'Mozilla/5.0 (compatible; AhrefsBot/3.0; +http://ahrefs.com/robot/)' => 'AhrefsBot/3.0',
				'Mozilla/5.0 (compatible; AhrefsBot/3.1; +http://ahrefs.com/robot/)' => 'AhrefsBot/3.1',
				'Mozilla/5.0 (compatible; AhrefsBot/4.0; +http://ahrefs.com/robot/)' => 'AhrefsBot/4.0',
				'Mozilla/5.0 (compatible; AhrefsBot/5.0; +http://ahrefs.com/robot/)' => 'AhrefsBot/5.0',
			),
			'aiHitBot' => array(
				'Mozilla/5.0 (compatible; aiHitBot-DM/2.0.2 +http://www.aihit.com)' => 'aiHitBot-DM/2.0.2',
				'Mozilla/5.0 (compatible; aiHitBot/1.0-DS; +http://www.aihit.com/)' => 'aiHitBot/1.0-DS',
				'Mozilla/5.0 (compatible; aiHitBot/1.0; +http://www.aihit.com/)' => 'aiHitBot/1.0',
				'Mozilla/5.0 (compatible; aiHitBot/1.1; +http://www.aihit.com/)' => 'aiHitBot/1.1',
				'Mozilla/5.0 (compatible; aiHitBot-BP/1.1; +http://www.aihit.com/)' => 'aiHitBot-BP/1.1',
				'Mozilla/5.0 (compatible; aiHitBot/2.7; +http://www.aihit.com/)' => 'aiHitBot/2.7',
				'Mozilla/5.0 (compatible; aiHitBot/2.8; +http://endb-consolidated.aihit.com/)' => 'aiHitBot/2.8',
			),
			'aippie' => array(
				'appie 1.1 (www.walhello.com)' => 'appie 1.1',
			),
			'akula' => array(
				'Mozilla/5.0 (compatible; akula/k311; +http://k311.fd.cvut.cz/)' => 'akula/k311',
				'Mozilla/5.0 (compatible; akula/12.0rc-2; +http://k311.fd.cvut.cz/)' => 'akula/12.0rc-2',
			),
			'alexa site audit' => array(
				'Mozilla/5.0 (compatible; alexa site audit/1.0; +http://www.alexa.com/help/webmasters; siteaudit@alexa.com)' => 'alexa site audit/1.0',
			),
			'Alexabot' => array(
				'Mozilla/5.0 (compatible; Alexabot/1.0; +http://www.alexa.com/help/certifyscan; certifyscan@alexa.com)' => 'Alexabot/1.0',
			),
			'Almaden' => array(
				'http://www.almaden.ibm.com/cs/crawler   [bc22]' => 'Almaden bc22',
				'http://www.almaden.ibm.com/cs/crawler   [hc4]' => 'Almaden hc4',
				'http://www.almaden.ibm.com/cs/crawler   [bc14]' => 'Almaden bc14',
				'http://www.almaden.ibm.com/cs/crawler   [bc5]' => 'Almaden bc5',
				'http://www.almaden.ibm.com/cs/crawler   [fc13]' => 'Almaden fc13',
				'http://www.almaden.ibm.com/cs/crawler   [bc6]' => 'Almaden bc6',
				'http://www.almaden.ibm.com/cs/crawler   [bc12]' => 'Almaden bc12',
				'http://www.almaden.ibm.com/cs/crawler' => 'Almaden',
			),
			'Amagit.COM' => array(
				'http://www.amagit.com/' => 'Amagit.COM',
			),
			'Amfibibot' => array(
				'Amfibibot/0.07 (Amfibi Robot; http://www.amfibi.com; agent@amfibi.com)' => 'Amfibibot/0.07',
			),
			'amibot' => array(
				'amibot - http://www.amidalla.de - tech@amidalla.com libwww-perl/5.831' => 'amibot',
			),
			'AMZNKAssocBot' => array(
				'Mozilla/5.0 (compatible; AMZNKAssocBot/4.0 +http://affiliate-program.amazon.com)' => 'AMZNKAssocBot/4.0',
			),
			'AntBot' => array(
				'Mozilla/5.0 (compatible; AntBot/1.0; +http://www.ant.com/)' => 'AntBot/1.0',
				'AntBot/1.0 (http://www.ant.com)' => 'AntBot/1.0',
			),
			'Apercite' => array(
				'Mozilla/5.0 (compatible; Apercite; +http://www.apercite.fr/robot/index.html)' => 'Apercite',
			),
			'AportWorm' => array(
				'Mozilla/5.0 (compatible; AportWorm/3.2; +http://www.aport.ru/help)' => 'AportWorm/3.2',
			),
			'AraBot' => array(
				'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6 Ara.com.tr AraBot 1.0' => 'AraBot 1.0',
			),
			'arachnode.net' => array(
				'http://arachnode.net 1.2' => 'arachnode.net/1.2',
				'http://arachnode.net 2.5' => 'arachnode.net/2.5',
			),
			'Arachnophilia' => array(
				'Mozilla/5.0 (compatible; Arachnophilia/1.0; +http://arachnys.com/)' => 'Arachnophilia/1.0',
			),
			'archive.org_bot' => array(
				'Mozilla/5.0 (compatible; archive.org_bot +http://www.archive.org/details/archive.org_bot)' => 'archive.org_bot',
				'Mozilla/5.0 (compatible; special_archiver/3.1.1 +http://www.archive.org/details/archive.org_bot)' => 'special_archiver/3.1.1',
				'Mozilla/5.0 (compatible; archive.org_bot; Wayback Machine Live Record; +http://archive.org/details/archive.org_bot)' => 'archive.org_bot',
			),
			'ASAHA Search Engine Turkey' => array(
				'ASAHA Search Engine Turkey V.001 (http://www.asaha.com/)' => 'ASAHA Search Engine Turkey V.001',
			),
			'Ask Jeeves/Teoma' => array(
				'Mozilla/2.0 (compatible; Ask Jeeves/Teoma; +http://sp.ask.com/docs/about/tech_crawling.html)' => 'Ask Jeeves/Teoma - b',
				'Mozilla/2.0 (compatible; Ask Jeeves/Teoma)' => 'Ask Jeeves/Teoma',
				'Mozilla/2.0 (compatible; Ask Jeeves/Teoma; +http://about.ask.com/en/docs/about/webmasters.shtml)' => 'Ask Jeeves/Teoma - c',
				'Mozilla/5.0 (compatible; Ask Jeeves/Teoma; +http://about.ask.com/en/docs/about/webmasters.shtml)' => 'Ask Jeeves/Teoma',
				'Mozilla/2.0 (compatible; Ask Jeeves/Teoma; http://about.ask.com/en/docs/about/webmasters.shtml)' => 'Ask Jeeves/Teoma - d',
			),
			'AskQuickly' => array(
				'AskQuickly v2 (http://askquickly.org/)' => 'AskQuickly v2',
			),
			'Automattic Analytics Crawler' => array(
				'Automattic Analytics Crawler/0.1; http://wordpress.com/crawler/' => 'Automattic Analytics Crawler/0.1',
			),
			'BabalooSpider' => array(
				'BabalooSpider/1.3 (BabalooSpider; http://www.babaloo.si; spider@babaloo.si)' => 'BabalooSpider/1.3',
			),
			'backlink-check.de' => array(
				'Backlink-Ceck.de (+http://www.backlink-check.de/bot.html)' => 'backlink-check.de',
			),
			'BacklinkCrawler' => array(
				'BacklinkCrawler (http://www.backlinktest.com/crawler.html)' => 'BacklinkCrawler',
				'BacklinkCrawler V (http://www.backlinktest.com/crawler.html)' => 'BacklinkCrawler V',
			),
			'Bad-Neighborhood' => array(
				'Bad-Neighborhood Link Analyzer (http://www.bad-neighborhood.com/)' => 'Bad-Neighborhood Link Analyzer',
				'Bad Neighborhood Header Detector (http://www.bad-neighborhood.com/header_detector.php)' => 'Bad Neighborhood Header Detector',
			),
			'Baiduspider' => array(
				'Baiduspider+(+http://www.baidu.com/search/spider.htm)' => 'Baiduspider',
				'Baiduspider+(+http://www.baidu.jp/spider/)' => 'Baiduspider japan',
				'Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)' => 'Baiduspider/2.0',
				'Baiduspider-image+(+http://www.baidu.com/search/spider.htm)' => 'Baiduspider-image',
			),
			'baypup' => array(
				'baypup/colbert (Baypup; http://sf.baypup.com/webmasters; jason@baypup.com)' => 'baypup/colbert',
				'baypup/1.1 (Baypup; http://www.baypup.com/; jason@baypup.com)' => 'baypup/1.1',
				'baypup/colbert (Baypup; http://www.baypup.com/webmasters; jason@baypup.com)' => 'baypup/colbert',
			),
			'BDCbot' => array(
				'Mozilla/5.0 (Windows NT 6.1; compatible; BDCbot/1.0; +http://ecommerce.bigdatacorp.com.br/faq.aspx) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.107 Safari/535.1' => 'BDCbot/1.0',
			),
			'BDFetch' => array(
				'BDFetch' => 'BDFetch',
			),
			'BecomeBot' => array(
				'Mozilla/5.0 (compatible; BecomeBot/2.3; MSIE 6.0 compatible; +http://www.become.com/site_owners.html)' => 'BecomeBot/2.3',
				'Mozilla/5.0 (compatible; BecomeBot/3.0; MSIE 6.0 compatible; +http://www.become.com/site_owners.html)' => 'BecomeBot/3.0',
				'Mozilla/5.0 (compatible; BecomeBot/3.0; +http://www.become.com/site_owners.html)' => 'BecomeBot/3.0 b',
				'Mozilla/5.0 (compatible; BecomeJPBot/2.3; MSIE 6.0 compatible; +http://www.become.co.jp/site_owners.html)' => 'BecomeBot/2.3 b',
			),
			'BegunAdvertising' => array(
				'Mozilla/5.0 (compatible; BegunAdvertising/3.0; +http://begun.ru/begun/technology/indexer/)' => 'BegunAdvertising/3.0',
			),
			'Bigsearch.ca' => array(
				'Bigsearch.ca/Nutch-0.9-dev (Bigsearch.ca Internet Spider; http://www.bigsearch.ca/; info@enhancededge.com)' => 'Bigsearch.ca/Nutch-0.9-dev',
				'Bigsearch.ca/Nutch-1.0-dev (Bigsearch.ca Internet Spider; http://www.bigsearch.ca/; info@enhancededge.com)' => 'Bigsearch.ca/Nutch-1.0-dev',
			),
			'bingbot' => array(
				'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)' => 'bingbot/2.0',
				'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm' => 'bingbot/2.0',
				'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) SitemapProbe' => 'bingbot SitemapProbe',
				'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/534+ (KHTML, like Gecko) BingPreview/1.0b' => 'BingPreview/1.0b',
				'Mozilla/5.0 (seoanalyzer; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)' => 'bingbot/2.0 seoanalyser',
			),
			'bitlybot' => array(
				'bitlybot' => 'bitlybot',
			),
			'biwec' => array(
				'Mozilla/5.0 (compatible; socketcrawler; http://nlp.fi.muni.cz/projects/biwec/)' => 'biwec',
			),
			'bixocrawler' => array(
				'Mozilla/5.0 (compatible; ptd-crawler; +http://bixolabs.com/crawler/ptd/; crawler@bixolabs.com)' => 'ptd-crawler',
				'Mozilla/5.0 (compatible; bixolabs/1.0; +http://bixolabs.com/crawler/general; crawler@bixolabs.com)' => 'bixolabs/1.0',
				'Mozilla/5.0 (compatible; bixolabs/1.0; +http://bixolabs.com/crawler/general; crawler@mail.bixolabs.com)' => 'bixolabs/1.0',
				'Mozilla/5.0 (compatible; Finderbots finder bot; +http://wiki.github.com/bixo/bixo/bixocrawler; bixo-dev@yahoogroups.com)' => 'bixo',
				'Mozilla/5.0 (compatible; Mozilla; +http://wiki.github.com/bixo/bixo/bixocrawler; bixo-dev@yahoogroups.com)' => 'bixocrawler',
				'Mozilla/5.0 (compatible; BIXOCRAWLER; +http://wiki.github.com/bixo/bixo/bixocrawler; bixo-dev@yahoogroups.com)' => 'bixocrawler',
				'Mozilla/5.0 (compatible; Mozilla/5.0; +http://wiki.github.com/bixo/bixo/bixocrawler; bixo-dev@yahoogroups.com)' => 'bixocrawler',
				'Mozilla/5.0 (compatible; websays; +http://wiki.github.com/bixo/bixo/bixocrawler; bixo-dev@yahoogroups.com)' => 'bixocrawler',
				'Mozilla/5.0 (compatible; TestCrawler; +http://wiki.github.com/bixo/bixo/bixocrawler; bixo-dev@yahoogroups.com)' => 'bixocrawler TestCrawler',
				'Mozilla/5.0 (compatible; adbeat-publisher-description-fetcher; +crawler@scaleunlimited.com; crawler@scaleunlimited.com)' => 'adbeat-publisher-description-fetcher',
			),
			'bl.uk_lddc_bot' => array(
				'bl.uk_lddc_bot/3.1.1 (+http://www.bl.uk/aboutus/legaldeposit/websites/websites/faqswebmaster/index.html)' => 'bl.uk_lddc_bot/3.1.1',
			),
			'Blaiz-Bee' => array(
				'Blaiz-Bee/2.00.5622 (+http://www.blaiz.net)' => 'Blaiz-Bee/2.00.5622',
				'Blaiz-Bee/2.00.5655 (+http://www.blaiz.net)' => 'Blaiz-Bee/2.00.5655',
				'Blaiz-Bee/2.00.6082 (+http://www.blaiz.net)' => 'Blaiz-Bee/2.00.6082',
				'Blaiz-Bee/2.00.8315 (BE Internet Search Engine http://www.rawgrunt.com)' => 'Blaiz-Bee/2.00.8315',
			),
			'Blekkobot' => array(
				'Mozilla/5.0 (compatible; Blekkobot; ScoutJet; +http://blekko.com/about/blekkobot)' => 'Blekkobot',
			),
			'BLEXBot' => array(
				'BLEXBot' => 'BLEXBot',
				'Mozilla/5.0 (compatible; BLEXBot/1.0; +http://webmeup.com/crawler.html)' => 'BLEXBot/1.0',
				'Mozilla/5.0 (compatible; BLEXBot/1.0; +http://webmeup-crawler.com/)' => 'BLEXBot/1.0',
				'Mozilla/5.0 (compatible; BLEXBotTest/1.0; +http://webmeup.com/crawler.html)' => 'BLEXBotTest/1.0',
			),
			'BlinkaCrawler' => array(
				'Mozilla/5.0 (compatible; BlinkaCrawler/1.0; +http://www.blinka.jp/crawler/)' => 'BlinkaCrawler/1.0',
			),
			'Bloggsi' => array(
				'Bloggsi/1.0 (http://bloggsi.com/)' => 'Bloggsi/1.0',
			),
			'BlogPulse' => array(
				'BlogPulseLive (support@blogpulse.com)' => 'BlogPulseLive',
				'BlogPulse (ISSpider-3.0)' => 'BlogPulse',
			),
			'bnf.fr_bot' => array(
				'Mozilla/5.0 (compatible; bnf.fr_bot; +http://www.bnf.fr/fr/outils/a.dl_web_capture_robot.html)' => 'bnf.fr_bot',
			),
			'boitho.com-dc' => array(
				'boitho.com-dc/0.83 ( http://www.boitho.com/dcbot.html )' => 'boitho.com-dc/0.83',
				'boitho.com-dc/0.79 ( http://www.boitho.com/dcbot.html )' => 'boitho.com-dc/0.79',
				'boitho.com-dc/0.85 ( http://www.boitho.com/dcbot.html )' => 'boitho.com-dc/0.85',
				'boitho.com-dc/0.86 ( http://www.boitho.com/dcbot.html )' => 'boitho.com-dc/0.86',
				'boitho.com-dc/0.82 ( http://www.boitho.com/dcbot.html )' => 'boitho.com-dc/0.82',
			),
			'bot-pge.chlooe.com' => array(
				'bot-pge.chlooe.com/1.0.0 (+http://www.chlooe.com/)' => 'bot-pge.chlooe.com/1.0.0',
			),
			'bot.wsowner.com' => array(
				'bot.wsowner.com/1.0.0 (+http://wsowner.com/)' => 'bot.wsowner.com/1.0.0',
			),
			'botmobi' => array(
				'Nokia6680/1.0 (4.04.07) SymbianOS/8.0 Series60/2.6 Profile/MIDP-2.0 Configuration/CLDC-1.1 (botmobi find.mobi/bot.html find@mtld.mobi)' => 'botmobi',
			),
			'BotOnParade' => array(
				'BotOnParade, http://www.bots-on-para.de/bot.html' => 'BotOnParade',
			),
			'BrainbruBot' => array(
				'BrainbruBot/1.0 (+http://www.brainbru.com/)' => 'BrainbruBot/1.0',
			),
			'Browsershots' => array(
				'Browsershots' => 'Browsershots',
			),
			'btbot' => array(
				'btbot/0.4 (+http://www.btbot.com/btbot.html)' => 'btbot/0.4',
			),
			'BUbiNG' => array(
				'BUbiNG (+http://law.di.unimi.it/BUbiNG.html)' => 'BUbiNG',
			),
			'Butterfly' => array(
				'Mozilla/5.0 (compatible; Butterfly/1.0; +http://labs.topsy.com/butterfly.html) Gecko/2009032608 Firefox/3.0.8' => 'Butterfly/1.0',
				'Mozilla/5.0 (compatible; Butterfly/1.0; +http://labs.topsy.com/butterfly/) Gecko/2009032608 Firefox/3.0.8' => 'Butterfly/1.0 a',
			),
			'BuzzRankingBot' => array(
				'Mozilla/5.0 (compatible; BuzzRankingBot/1.0; +http://www.buzzrankingbot.com/)' => 'BuzzRankingBot/1.0',
			),
			'CamontSpider' => array(
				'CamontSpider/1.0 +http://epweb2.ph.bham.ac.uk/user/slater/camont/info.html' => 'CamontSpider/1.0',
			),
			'CareerBot' => array(
				'Mozilla/5.0 (compatible; CareerBot/1.1; +http://www.career-x.de/bot.html)' => 'CareerBot/1.1',
			),
			'Castabot' => array(
				'Castabot/0.1 (+http://topixtream.com/)' => 'Castabot/0.1',
			),
			'CatchBot' => array(
				'CatchBot/1.0; +http://www.catchbot.com' => 'CatchBot/1.0',
				'CatchBot/3.0; +http://www.catchbot.com' => 'CatchBot/3.0',
				'CatchBot/2.0; +http://www.catchbot.com' => 'CatchBot/2.0',
				'CatchBot/5.0; +http://www.catchbot.com' => 'CatchBot/5.0',
			),
			'CazoodleBot' => array(
				'Cazoodle/Nutch-0.9-dev (Cazoodle Nutch Crawler; http://www.cazoodle.com; mqbot@cazoodle.com)' => 'CazoodleBot a',
				'CazoodleBot/Nutch-0.9-dev (CazoodleBot Crawler; http://www.cazoodle.com; mqbot@cazoodle.com)' => 'CazoodleBot d',
				'CazoodleBot/0.1 (CazoodleBot Crawler; http://www.cazoodle.com; mqbot@cazoodle.com)' => 'CazoodleBot b',
				'CazoodleBot/Nutch-0.9-dev (CazoodleBot Crawler; http://www.cazoodle.com/cazoodlebot; cazoodlebot@cazoodle.com)' => 'CazoodleBot c',
				'CazoodleBot/CazoodleBot-0.1 (CazoodleBot Crawler; http://www.cazoodle.com/cazoodlebot; cazoodlebot@cazoodle.com)' => 'CazoodleBot-0.1',
				'CazoodleBot/0.0.2 (http://www.cazoodle.com/contact.php; cbot@cazoodle.com)' => 'CazoodleBot/0.0.2',
			),
			'CCBot' => array(
				'CCBot/1.0 (+http://www.commoncrawl.org/bot.html)' => 'CCBot/1.0',
				'CCBot/2.0' => 'CCBot/2.0',
				'CCBot/2.0 (http://commoncrawl.org/faq/)' => 'CCBot/2.0',
			),
			'CCResearchBot' => array(
				'CCResearchBot/1.0 commoncrawl.org/research//Nutch-1.7-SNAPSHOT' => 'CCResearchBot/1.0',
			),
			'ccubee' => array(
				'ccubee/3.2' => 'ccubee/3.2',
				'ccubee/3.3' => 'ccubee/3.3',
				'ccubee/3.7' => 'ccubee/3.7',
				'ccubee/4.0' => 'ccubee/4.0',
				'ccubee/3.5' => 'ccubee/3.5',
				'ccubee/9.0' => 'ccubee/9.0',
				'ccubee/10.0' => 'ccubee/10.0',
				'ccubee/2008' => 'ccubee/2008',
			),
			'ChangeDetection' => array(
				'mozilla/4.0 (compatible; changedetection/1.0 (admin@changedetection.com))' => 'changedetection/1.0',
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1;  http://www.changedetection.com/bot.html )' => 'ChangeDetection',
			),
			'Charlotte' => array(
				'Mozilla/5.0 (compatible; Charlotte/1.1; http://www.searchme.com/support/)' => 'Charlotte/1.1',
			),
			'CirrusExplorer' => array(
				'CirrusExplorer/1.1 (http://www.cireu.com/explorer.php)' => 'CirrusExplorer/1.1',
			),
			'City4you' => array(
				'City4you/1.3 Cesky (+http://www.city4you.pl)' => 'City4you/1.3 Cesky',
			),
			'cityreview' => array(
				'Cityreview Robot (+http://www.cityreview.org/crawler/)' => 'cityreview',
			),
			'CJB.NET Proxy' => array(
				'CJB.NET Proxy' => 'CJB.NET Proxy',
			),
			'classbot' => array(
				'classbot (+http://allclasses.com)' => 'classbot',
			),
			'CligooRobot' => array(
				'Mozilla/5.0 (compatible; CligooRobot/2.0; +http://www.cligoo.de/wk/technik.php)' => 'CligooRobot/2.0',
			),
			'CliqzBot' => array(
				'Cliqz Bot (+http://www.cliqz.com)' => 'Cliqz Bot',
			),
			'Cliqzbot' => array(
				'Cliqzbot/0.1 (+http://cliqz.com +cliqzbot@cliqz.com)' => 'Cliqzbot/0.1',
				'Cliqzbot/0.1 (+http://cliqz.com/company/cliqzbot)' => 'Cliqzbot/0.1',
			),
			'CloudFlare-AlwaysOnline' => array(
				'Mozilla/5.0 (compatible; CloudFlare-AlwaysOnline/1.0; +http://www.cloudflare.com/always-online) AppleWebKit/534.34' => 'CloudFlare-AlwaysOnline/1.0',
			),
			'CloudServerMarketSpider' => array(
				'Mozilla/5.0 (compatible; CloudServerMarketSpider/1.0; +http://www.cloudservermarket.com/spider.html)' => 'CloudServerMarketSpider/1.0',
			),
			'CMS Crawler' => array(
				'Mozilla/4.0 (CMS Crawler: http://www.cmscrawler.com)' => 'CMS Crawler',
			),
			'coccoc' => array(
				'coccoc' => 'coccoc',
				'coccoc/1.0 ()' => 'coccoc/1.0',
				'coccoc/1.0 (http://help.coccoc.vn/)' => 'coccoc/1.0',
				'coccoc/1.0 (http://help.coccoc.com/)' => 'coccoc/1.0',
				'Mozilla/5.0 (compatible; coccoc/1.0; +http://help.coccoc.com/)' => 'coccoc/1.0',
			),
			'Combine' => array(
				'Combine/3 http://combine.it.lth.se/' => 'Combine/3',
			),
			'Company News Search engine' => array(
				'CorporateNewsSearchEngine/Nutch-1.7 (http://pibs.co/news-search-engine)' => 'Company News Search engine',
			),
			'CompSpyBot' => array(
				'Mozilla/5.0 (compatible; CompSpyBot/1.0; +http://www.compspy.com/spider.html)' => 'CompSpyBot/1.0',
			),
			'ConveraCrawler' => array(
				'ConveraMultiMediaCrawler/0.1 (+http://www.authoritativeweb.com/crawl)' => 'ConveraMultiMediaCrawler/0.1',
				'ConveraCrawler/0.9d (+http://www.authoritativeweb.com/crawl)' => 'ConveraCrawler 0.9d',
				'ConveraCrawler/0.9e (+http://www.authoritativeweb.com/crawl)' => 'ConveraCrawler 0.9e',
			),
			'copyright sheriff' => array(
				'copyright sheriff (+http://www.copyrightsheriff.com/)' => 'copyright sheriff',
			),
			'CorpusCrawler' => array(
				'CorpusCrawler 2.0.0 (http://corpora.fi.muni.cz/crawler/)' => 'CorpusCrawler 2.0.0',
				'CorpusCrawler 2.0.8 (http://corpora.fi.muni.cz/crawler/)' => 'CorpusCrawler 2.0.8',
				'CorpusCrawler 2.0.9 (http://corpora.fi.muni.cz/crawler/)' => 'CorpusCrawler 2.0.9',
				'CorpusCrawler 2.0.10 (http://corpora.fi.muni.cz/crawler/)' => 'CorpusCrawler 2.0.10',
				'CorpusCrawler 2.0.15 (http://corpora.fi.muni.cz/crawler/)' => 'CorpusCrawler 2.0.15',
				'CorpusCrawler 2.0.12 (http://corpora.fi.muni.cz/crawler/)' => 'CorpusCrawler 2.0.12',
				'CorpusCrawler 2.0.13 (http://corpora.fi.muni.cz/crawler/)' => 'CorpusCrawler 2.0.13',
				'CorpusCrawler 2.0.14 (http://corpora.fi.muni.cz/crawler/)' => 'CorpusCrawler 2.0.14',
				'CorpusCrawler 2.0.22 (http://corpora.fi.muni.cz/crawler/);Project:CzCorpus' => 'CorpusCrawler 2.0.22',
				'CorpusCrawler 2.0.24 (http://corpora.fi.muni.cz/crawler/);Project:CzCorpus' => 'CorpusCrawler 2.0.24',
				'CorpusCrawler 2.0.25 (http://corpora.fi.muni.cz/crawler/);Project:CzCorpus' => 'CorpusCrawler 2.0.25',
				'CorpusCrawler 2.0.17 (http://corpora.fi.muni.cz/crawler/);Project:CzCorpus' => 'CorpusCrawler 2.0.17',
				'CorpusCrawler 2.0.19 (http://corpora.fi.muni.cz/crawler/);Project:CzCorpus' => 'CorpusCrawler 2.0.19',
				'CorpusCrawler 2.0.20 (http://corpora.fi.muni.cz/crawler/);Project:CzCorpus' => 'CorpusCrawler 2.0.20',
				'CorpusCrawler 2.0.21 (http://corpora.fi.muni.cz/crawler/);Project:CzCorpus' => 'CorpusCrawler 2.0.21',
			),
			'Covario-IDS' => array(
				'Covario-IDS/1.0 (Covario; http://www.covario.com/ids; support at covario dot com)' => 'Covario-IDS/1.0',
				'CovarioIDS/1.1 (http://www.covario.com/ids; support at covario dot com)' => 'CovarioIDS/1.1',
			),
			'crawler for netopian' => array(
				'crawler for netopian (http://www.netopian.co.uk/)' => 'crawler for netopian',
			),
			'Crawler4j' => array(
				'crawler4j (http://code.google.com/p/crawler4j/)' => 'Crawler4j',
			),
			'Crowsnest' => array(
				'Crowsnest/0.5 (+http://www.crowsnest.tv/)' => 'Crowsnest/0.5',
			),
			'csci_b659' => array(
				'csci_b659/0.13' => 'csci_b659/0.13',
			),
			'Curious George' => array(
				'Curious George - www.analyticsseo.com/crawler' => 'Curious George',
			),
			'DataFountains' => array(
				'DataFountains/Dmoz Downloader (http://ivia.ucr.edu/useragents.shtml)' => 'DataFountains at Dmoz',
				'DataFountains/DMOZ Feature Vector Corpus Creator (http://ivia.ucr.edu/useragents.shtml)' => 'DataFountains at Dmoz b',
			),
			'datagnionbot' => array(
				'datagnionbot (+http://www.datagnion.com/bot.html)' => 'datagnionbot',
			),
			'DataparkSearch' => array(
				'DataparkSearch/4.40.1 (+http://www.dataparksearch.org/)' => 'DataparkSearch/4.40',
			),
			'Daumoa' => array(
				'Mozilla/4.0 (compatible; MSIE is not me; DAUMOA/1.0.0; DAUM Web Robot; Daum Communications Corp., Korea)' => 'DAUMOA/1.0.0',
				'Mozilla/4.0 (compatible; MSIE is not me; DAUMOA/1.0.1; DAUM Web Robot; Daum Communications Corp., Korea)' => 'DAUMOA/1.0.1',
				'Mozilla/4.0 (compatible; MSIE enviable; DAUMOA/1.0.1; DAUM Web Robot; Daum Communications Corp., Korea; +http://ws.daum.net/aboutkr.html)' => 'DAUMOA/1.0.1',
				'Mozilla/4.0 (compatible; MSIE enviable; DAUMOA 2.0; DAUM Web Robot; Daum Communications Corp., Korea; +http://ws.daum.net/aboutkr.html)' => 'Daumoa/2.0',
				'Mozilla/5.0 (compatible; MSIE or Firefox mutant; not on Windows server; +http://ws.daum.net/aboutWebSearch.html) Daumoa/2.0' => 'Daumoa/2.0',
				'Mozilla/5.0 (compatible; MSIE or Firefox mutant; not on Windows server; +http://ws.daum.net/aboutWebSearch.html) Daumoa/3.0' => 'Daumoa/3.0',
				'Mozilla/5.0 (compatible; MSIE or Firefox mutant; not on Windows server; + http://tab.search.daum.net/aboutWebSearch.html) Daumoa/3.0' => 'Daumoa/3.0',
				'Mozilla/5.0 (compatible; MSIE or Firefox mutant; not on Windows server;) Daumoa/4.0' => 'Daumoa/4.0',
			),
			'DBLBot' => array(
				'Mozilla/5.0 (compatible; DBLBot/1.0; +http://www.dontbuylists.com/)' => 'DBLBot/1.0',
			),
			'DCPbot' => array(
				'Mozilla/5.0 (compatible; DCPbot/1.0; +http://domains.checkparams.com/)' => 'DCPbot/1.0',
				'Mozilla/5.0 (compatible; DCPbot/1.1; +http://domains.checkparams.com/)' => 'DCPbot/1.1',
			),
			'DealGates Bot' => array(
				'DealGates Bot/1.1 by Luc Michalski (http://spider.dealgates.com/bot.html)' => 'DealGates Bot/1.1',
			),
			'del.icio.us-thumbnails' => array(
				'Mozilla/5.0 (compatible; del.icio.us-thumbnails/1.0; FreeBSD) KHTML/4.3.2 (like Gecko)' => 'del.icio.us-thumbnails/1.0',
				'del.icio.us-thumbnails/1.0 Mozilla/5.0 (compatible; Konqueror/3.4; FreeBSD) KHTML/3.4.2 (like Gecko)' => 'del.icio.us-thumbnails/1.0',
			),
			'DepSpid' => array(
				'Mozilla/4.0 (compatible; DepSpid/5.07; +http://about.depspid.net)' => 'DepSpid/5.07',
				'depspid - the dependency spider' => 'DepSpid',
				'Mozilla/4.0 (compatible; DepSpid/5.10; +http://about.depspid.net)' => 'DepSpid/5.10',
				'Mozilla/4.0 (compatible; DepSpid/5.24; +http://about.depspid.net)' => 'DepSpid/5.24',
				'Mozilla/4.0 (compatible; DepSpid/5.25; +http://about.depspid.net)' => 'DepSpid/5.25',
				'Mozilla/4.0 (compatible; DepSpid/5.26; +http://about.depspid.net)' => 'DepSpid/5.26',
			),
			'discoverybot' => array(
				'mozilla/5.0 (compatible; discobot/1.1; +http://discoveryengine.com/discobot.html)' => 'discobot/1.1',
				'Mozilla/5.0 (compatible; discobot/1.0; +http://discoveryengine.com/discobot.html)' => 'discobot/1.0',
				'Mozilla/5.0 (compatible; discobot/2.0; +http://discoveryengine.com/discobot.html)' => 'discobot/2.0',
				'Mozilla/5.0 (compatible; discoverybot/2.0; +http://discoveryengine.com/discoverybot.html)' => 'discoverybot/2.0',
			),
			'DKIMRepBot' => array(
				'Mozilla/5.0 (compatible; DKIMRepBot/1.0; +http://www.dkim-reputation.org)' => 'DKIMRepBot/1.0',
			),
			'dlcbot' => array(
				'Mozilla/5.0 (compatible; dlcbot/0.1; +http://www.drlinkcheck.com/)' => 'dlcbot/0.1',
			),
			'dlvr.it' => array(
				'dlvr.it/1.0 (+http://dlvr.it/) Mozilla/5.0' => 'dlvr.it/1.0',
			),
			'Dlvr.it/1.0' => array(
				'Dlvr.it/1.0 (http://dlvr.it/)' => 'Dlvr.it/1.0',
			),
			'DNS-Digger-Explorer' => array(
				'Mozilla/5.0 (compatible; DNS-Digger-Explorer/1.0; +http://www.dnsdigger.com)' => 'DNS-Digger-Explorer/1.0',
			),
			'DomainDB' => array(
				'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0; DomainDB-1.1; http://domaindb.com/crawler/)' => 'DomainDB/1.1',
			),
			'Dot TK - spider' => array(
				'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.0.5) Gecko/2010033101 Gentoo Firefox/3.0.5 (Dot TK - spider 3.0)' => 'Dot TK - spider 3.0',
			),
			'DotBot' => array(
				'Mozilla/5.0 (compatible; DotBot/1.1; http://www.dotnetdotcom.org/, crawler@dotnetdotcom.org)' => 'DotBot/1.1',
				'Mozilla/5.0 (compatible; DotBot/1.1; http://www.opensiteexplorer.org/dotbot, help@moz.com)' => 'DotBot/1.1',
			),
			'dotSemantic' => array(
				'Mozilla/5.0 (compatible; dotSemantic/1.0; +http://www.dotsemantic.org)' => 'dotSemantic/1.0',
			),
			'DripfeedBot' => array(
				'Mozilla/5.0 (compatible; DripfeedBot/2.0; +http://dripfeedbookmark.com/bot.html' => 'DripfeedBot/2.0',
			),
			'drupact' => array(
				'drupact/0.7; http://www.arocom.de/drupact' => 'drupact/0.7',
			),
			'DuckDuckBot' => array(
				'DuckDuckBot/1.0; (+http://duckduckgo.com/duckduckbot.html)' => 'DuckDuckBot/1.0',
			),
			'DuckDuckPreview' => array(
				'DuckDuckPreview/1.0; (+http://duckduckgo.com/duckduckpreview.html)' => 'DuckDuckPreview/1.0',
			),
			'e-SocietyRobot' => array(
				'e-SocietyRobot(http://www.yama.info.waseda.ac.jp/~yamana/es/)' => 'e-SocietyRobot',
			),
			'EasouSpider' => array(
				'Mozilla/5.0 (compatible; EasouSpider; +http://www.easou.com/search/spider.html)' => 'EasouSpider',
			),
			'EasyBib AutoCite' => array(
				'EasyBib AutoCite (http://content.easybib.com/autocite/)' => 'EasyBib AutoCite',
			),
			'eCairn-Grabber' => array(
				'eCairn-Grabber/1.0 (+http://ecairn.com/grabber) curl/7.15' => 'eCairn-Grabber/1.0',
			),
			'eCommerceBot' => array(
				'eCommerceBot (http://www.ehandel.se/botinfo.html)' => 'eCommerceBot',
			),
			'EDI' => array(
				'EDI/1.6.5 (Edacious & Intelligent Web Robot, Daum Communications Corp.)' => 'EDI/1.6.5',
				'Mozilla/4.0 (compatible; EDI/1.6.6; Edacious & Intelligent Web Robot; Daum Communications Corp., Korea)' => 'EDI/1.6.6',
				'Mozilla/4.0 (compatible; MSIE is not me; EDI/1.6.6; Edacious & Intelligent Web Robot; Daum Communications Corp., Korea)' => 'EDI/1.6.6 b',
			),
			'EdisterBot' => array(
				'EdisterBot (http://www.edister.com/bot.html)' => 'EdisterBot',
			),
			'egothor' => array(
				'egothor/8.0f (+http://ego.ms.mff.cuni.cz/)' => 'egothor/8.0f',
				'Mozilla/5.0 (compatible; egothor/8.0g; +http://ego.ms.mff.cuni.cz/)' => 'egothor/8.0g',
				'Mozilla/5.0 (compatible; egothor/11.0d; +http://ego.ms.mff.cuni.cz/)' => 'egothor/11.0d',
				'Mozilla/5.0 (compatible; egothor/11.0d; +https://kocour.ms.mff.cuni.cz/ego/)' => 'egothor/11.0d b',
				'Mozilla/5.0 (compatible; egothor/12.0rc-2; +http://ego.ms.mff.cuni.cz/)' => 'egothor/12.0rc-2',
			),
			'ejupiter.com' => array(
				'ejupiter.com' => 'ejupiter.com',
				'crawler43.ejupiter.com' => 'ejupiter.com 43',
			),
			'Embedly' => array(
				'Mozilla/5.0 (compatible; Embedly/0.2; +http://support.embed.ly/)' => 'Embedly/0.2',
			),
			'emefgebot' => array(
				'Mozilla/5.0 (compatible; emefgebot/beta; +http://emefge.de/bot.html)' => 'emefgebot/beta',
				'emefgebot/beta (+http://emefge.de/bot.html)' => 'emefgebot/beta',
			),
			'EnaBot' => array(
				'EnaBot/1.1 (http://www.enaball.com/crawler.html)' => 'EnaBot/1.1',
				'EnaBot/1.2 (http://www.enaball.com/crawler.html)' => 'EnaBot/1.2',
			),
			'Enterprise_Search' => array(
				'Enterprise_Search/1.00.143;MSSQL (http://www.innerprise.net/es-spider.asp)' => 'Enterprise_Search/1.00.143',
			),
			'envolk' => array(
				'envolk/1.7 (+http://www.envolk.com/envolkspiderinfo.html)' => 'envolk/1.7',
			),
			'Esribot' => array(
				'Mozilla/5.0 (compatible; Esribot/1.0; http://www.esrihu.hu/)' => 'Esribot/1.0',
			),
			'EuripBot' => array(
				'EuripBot/1.1 (+http://www.eurip.com) GetRobots' => 'EuripBot/1.1',
			),
			'Eurobot' => array(
				'Eurobot/1.1 (http://eurobot.ayell.eu)' => 'Eurobot/1.1',
				'Eurobot/1.2 (http://eurobot.ayell.eu)' => 'Eurobot/1.2',
			),
			'EventGuruBot' => array(
				'Mozilla/5.0 (compatible; EventGuruBot/1.0; +http://www.eventguru.com/spider.html)' => 'EventGuruBot/1.0',
			),
			'EveryoneSocialBot' => array(
				'Mozilla/5.0 (compatible; EveryoneSocialBot/1.0; support@everyonesocial.com http://everyonesocial.com/)' => 'EveryoneSocialBot/1.0',
			),
			'EvriNid' => array(
				'Mozilla/5.0 (compatible; Evrinid Iudex 1.0.0; +http://www.evri.com/evrinid)' => 'EvriNid/1.0.0',
			),
			'Exabot' => array(
				'Mozilla/5.0 (compatible; Exabot/3.0; +http://www.exabot.com/go/robot)' => 'Exabot/3.0',
				'Mozilla/5.0 (compatible; Exabot-Images/3.0; +http://www.exabot.com/go/robot)' => 'Exabot-Images/3.0',
				'Mozilla/5.0 (compatible; Exabot/3.0 (BiggerBetter); +http://www.exabot.com/go/robot)' => 'Exabot/3.0/BiggerBetter',
				'Mozilla/5.0 (compatible; Konqueror/3.5; Linux) KHTML/3.5.5 (like Gecko) (Exabot-Thumbnails)' => 'Exabot-Thumbnails',
				'Mozilla/5.0 (compatible; ExaleadCloudview/6;)' => 'ExaleadCloudview/6',
				'Mozilla/5.0 (compatible; ExaleadCloudView/5;)' => 'ExaleadCloudview/5',
			),
			'ExactSEEK' => array(
				'exactseek.com' => 'ExactSEEK',
			),
			'ExB Language Crawler' => array(
				'ExB Language Crawler 2.1.5 (+http://www.exb.de/crawler)' => 'ExB Language Crawler 2.1.5',
				'ExB Language Crawler 2.1.2 (+http://www.exb.de/crawler)' => 'ExB Language Crawler 2.1.2',
				'ExB Language Crawler 2.1.1 (+http://www.exb.de/crawler)' => 'ExB Language Crawler 2.1.1',
			),
			'Ezooms' => array(
				'Mozilla/5.0 (compatible; Ezooms/1.0; ezooms.bot@gmail.com)' => 'Ezooms/1.0',
				'Mozilla/5.0 (compatible; Ezooms/1.0; help@moz.com)' => 'Ezooms/1.0',
			),
			'FacebookExternalHit' => array(
				'facebookexternalhit/1.0 (+http://www.facebook.com/externalhit_uatext.php)' => 'FacebookExternalHit/1.0',
				'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)' => 'FacebookExternalHit/1.1',
			),
			'facebookplatform' => array(
				'facebookplatform/1.0 (+http://developers.facebook.com)' => 'facebookplatform/1.0',
			),
			'factbot' => array(
				'Factbot 1.09' => 'Factbot 1.09',
				'Factbot 1.09 (see http://www.factbites.com/webmasters.php)' => 'Factbot 1.09',
			),
			'FairShare' => array(
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1 + FairShare-http://fairshare.cc)' => 'FairShare',
			),
			'Falconsbot' => array(
				'Mozilla/5.0 (compatible; Falconsbot; +http://iws.seu.edu.cn/services/falcons/contact_us.jsp)' => 'Falconsbot',
				'Mozilla/5.0 (compatible; Falconsbot; +http://ws.nju.edu.cn/falcons/)' => 'Falconsbot',
			),
			'FAST Enterprise Crawler' => array(
				'FAST Enterprise Crawler/6.4 (crawler@fast.no)' => 'FAST Enterprise Crawler/6.4',
				'FAST Enterprise Crawler 6 used by FAST (jim.mosher@fastsearch.com)' => 'FAST Enterprise Crawler/6',
				'FAST Enterprise Crawler 6 used by Virk.dk - udvikling (thomas.bentzen@capgemini.com)' => 'FAST Enterprise Crawler 6 at virk.dk',
			),
			'FAST MetaWeb Crawler' => array(
				'FAST MetaWeb Crawler (helpdesk at fastsearch dot com)' => 'FAST MetaWeb Crawler',
			),
			'fastbot crawler' => array(
				'fastbot crawler beta 2.0 (+http://www.fastbot.de)' => 'fastbot crawler beta 2.0',
				'fastbot.de crawler 2.0 beta (http://www.fastbot.de)' => 'fastbot.de crawler beta 2.0',
			),
			'FauBot' => array(
				'Mozilla/5.0 (FauBot/0.1; +http://buzzvolume.com/fau/)' => 'FauBot/0.1',
			),
			'favorstarbot' => array(
				'favorstarbot/1.0 (+http://favorstar.com/bot.html)' => 'favorstarbot/1.0',
			),
			'FeedCatBot' => array(
				'FeedCatBot/3.0 (+http://www.feedcat.net/)' => 'FeedCatBot/3.0',
			),
			'FeedFinder/bloggz.se' => array(
				'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0; FeedFinder-2.0; http://bloggz.se/crawler)' => 'FeedFinder-2.0',
			),
			'Feedly' => array(
				'FeedlyBot/1.0 (http://feedly.com)' => 'FeedlyBot/1.0',
				'Feedly/1.0 (+http://www.feedly.com/fetcher.html; like FeedFetcher-Google)' => 'Feedly/1.0',
			),
			'Fetch-Guess' => array(
				'Fetch/2.0a (CMS Detection/Web/SEO analysis tool, see http://guess.scritch.org)' => 'Fetch/2.0a',
			),
			'Findexa Crawler' => array(
				'Findexa Crawler (http://www.findexa.no/gulesider/article26548.ece)' => 'Findexa Crawler',
			),
			'findlinks' => array(
				'findlinks/1.1.5-beta7 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/1.1.4-beta7',
				'findlinks/1.1.6-beta4 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/1.1.6-beta4',
				'findlinks/1.1.6-beta5 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/1.1.6-beta5',
				'findlinks/2.0 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/2.0',
				'findlinks/1.1.6-beta6 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/1.1.6-beta6',
				'findlinks/2.0.1 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/2.0.1',
				'findlinks/1.1.6-beta1 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/1.1.6-beta1',
				'findlinks/1.1.6-beta2 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/1.1.6-beta2',
				'findlinks/1.1.6-beta1 (+http://wortschatz.uni-leipzig.de/findlinks/; YaCy 0.1; yacy.net)' => 'findlinks/1.1.6-beta1 Yacy',
				'findlinks/1.1.6-beta3 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/1.1.6-beta3',
				'findlinks/2.0.2 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/2.0.2',
				'findlinks/2.0.4 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/2.0.4',
				'findlinks/1.1.3-beta9 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/1.1.3-beta9',
				'findlinks/2.0.9 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/2.0.9',
				'findlinks/2.1 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/2.1',
				'findlinks/2.1.3 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/2.1.3',
				'findlinks/2.1.5 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/2.1.5',
				'findlinks/2.2 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/2.2',
				'findlinks/2.0.5 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/2.0.5',
				'findlinks/2.5 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/2.5',
				'findlinks/2.6 (+http://wortschatz.uni-leipzig.de/findlinks/)' => 'findlinks/2.6',
			),
			'firmilybot' => array(
				'Mozilla/5.0 (compatible; firmilybot/0.3; +http://www.firmily.com/bot.php' => 'firmilybot/0.3',
			),
			'Flatland Industries Web Spider' => array(
				'flatlandbot/baypup (Flatland Industries Web Spider; http://www.flatlandindustries.com/flatlandbot; jason@flatlandindustries.com)' => 'flatlandbot/baypup',
			),
			'flatlandbot' => array(
				'great-plains-web-spider/gpws (Flatland Industries Web Spider; http://www.flatlandindustries.com/flatlandbot.php; jason@flatlandindustries.com)' => 'flatlandbot c',
				'great-plains-web-spider/flatlandbot (Flatland Industries Web Robot; http://www.flatlandindustries.com/flatlandbot.php; jason@flatlandindustries.com)' => 'flatlandbot b',
				'flatlandbot/flatlandbot (Flatland Industries Web Spider; http://www.flatlandindustries.com/flatlandbot.php; jason@flatlandindustries.com)' => 'flatlandbot',
				'great-plains-web-spider/flatlandbot (Flatland Industries Web Spider; http://www.flatlandindustries.com/flatlandbot.php; jason@flatlandindustries.com)' => 'flatlandbot d',
			),
			'FlightDeckReportsBot' => array(
				'FlightDeckReportsBot/2.0 (http://www.flightdeckreports.com/pages/bot)' => 'FlightDeckReportsBot/2.0',
			),
			'FlipboardProxy' => array(
				'Mozilla/5.0 (compatible; FlipboardProxy/1.1; +http://flipboard.com/browserproxy)' => 'FlipboardProxy/1.1',
				'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (FlipboardProxy/0.0.5; +http://flipboard.com/browserproxy)' => 'FlipboardProxy/0.0.5',
				'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (FlipboardProxy/1.1; +http://flipboard.com/browserproxy)' => 'FlipboardProxy/1.1',
				'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (FlipboardProxy/2.0; +http://flipboard.com/browserproxy)' => 'FlipboardProxy/2.0',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:28.0) Gecko/20100101 Firefox/28.0 (FlipboardProxy/1.1; +http://flipboard.com/browserproxy)' => 'FlipboardProxy/1.1',
			),
			'Flocke bot' => array(
				'http://www.uni-koblenz.de/~flocke/robot-info.txt' => 'Flocke bot',
			),
			'FollowSite Bot' => array(
				'FollowSite Bot ( http://www.followsite.com/bot.html )' => 'FollowSite Bot',
			),
			'Fooooo_Web_Video_Crawl' => array(
				'Mozilla/4.0 (compatible; Fooooo_Web_Video_Crawl http://fooooo.com/bot.html)' => 'Fooooo_Web_Video_Crawl',
			),
			'Forschungsportal' => array(
				'Forschungsportal/0.8-dev (Testinstallation; http://www.forschungsportal.net/; fpcrawler@rrzn.uni-hannover.de)' => 'Forschungsportal/0.8-dev',
			),
			'Francis' => array(
				'Francis/2.0 (francis@neomo.de http://www.neomo.de/pages/crawler.php)' => 'Francis/2.0',
			),
			'FreeWebMonitoring SiteChecker' => array(
				'FreeWebMonitoring SiteChecker/0.2 (+http://www.freewebmonitoring.com/bot.html)' => 'FreeWebMonitoring SiteChecker/0.2',
			),
			'FunnelBack' => array(
				'Mozilla/5.0 (compatible; FunnelBack; http://cyan.funnelback.com/robot.html)' => 'FunnelBack',
			),
			'FurlBot' => array(
				'Mozilla/4.0 compatible FurlBot/Furl Search 2.0 (FurlBot; http://www.furl.net; wn.furlbot@looksmart.net)' => 'FurlBot/Furl Search 2.0',
			),
			'FyberSpider' => array(
				'FyberSpider/1.2 (http://www.fybersearch.com/fyberspider.php)' => 'FyberSpider/1.2',
				'FyberSpider/1.3 (http://www.fybersearch.com/fyberspider.php)' => 'FyberSpider/1.3',
			),
			'g2crawler' => array(
				'g2Crawler (nobody@airmail.net)' => 'g2crawler',
			),
			'Gaisbot' => array(
				'Gaisbot/3.0+(robot@gais.cs.ccu.edu.tw;+http://gais.cs.ccu.edu.tw/robot.php)' => 'Gaisbot/3.0',
				'Gaisbot/3.0+(robot06@gais.cs.ccu.edu.tw;+http://gais.cs.ccu.edu.tw/robot.php)' => 'Gaisbot/3.0 - 06',
			),
			'Gallent Search Spider' => array(
				'Gallent Search Spider v1.4 Robot 3 (http://www.GallentSearch.com/robot)' => 'Gallent Search Spider v1.4 Robot 3',
			),
			'GarlikCrawler' => array(
				'GarlikCrawler/1.1 (http://garlik.com/, crawler@garik.com)' => 'GarlikCrawler/1.1',
				'GarlikCrawler/1.1 (http://garlik.com/, crawler@garlik.com)' => 'GarlikCrawler/1.1 b',
				'GarlikCrawler/1.2 (http://garlik.com/, crawler@garlik.com)' => 'GarlikCrawler/1.2',
			),
			'GeliyooBot' => array(
				'Mozilla/5.0 (compatible; GeliyooBot/1.0beta; +http://www.geliyoo.com/)' => 'GeliyooBot/1.0beta',
				'Mozilla/5.0 (compatible; GeliyooBot/1.0; +http://www.geliyoo.com/)' => 'GeliyooBot/1.0',
			),
			'genieBot' => array(
				'genieBot (wgao@genieknows.com)' => 'genieBot a',
				'genieBot ((http://64.5.245.11/faq/faq.html))' => 'genieBot b',
			),
			'Genieo Web filter' => array(
				'Mozilla/5.0 (compatible; Genieo/1.0 http://www.genieo.com/webfilter.html)' => 'Genieo/1.0',
				'Mozilla/5.0 (compatible; Genieo/1.0 http://www.genieo.com/webfilter.html) AppEngine-Google; (+http://code.google.com/appengine; appid: s~natmacdevice)' => 'Genieo/1.0',
			),
			'GeonaBot' => array(
				'GeonaBot/1.2; http://www.geona.com/' => 'GeonaBot/1.2',
			),
			'Giant/1.0' => array(
				'Giant/1.0 (Openmaru bot; robot@openmaru.com)' => 'Giant/1.0',
			),
			'Gigabot' => array(
				'Gigabot/3.0 (http://www.gigablast.com/spider.html)' => 'Gigabot/3.0',
				'Mozilla/5.0 (compatible; GigaBot/1.0; +http://www.gigablast.com/ )' => 'Gigabot/1.0',
			),
			'GingerCrawler' => array(
				'GingerCrawler/1.0 (Language Assistant for Dyslexics; www.gingersoftware.com/crawler_agent.htm; support at ginger software dot com)' => 'GingerCrawler/1.0',
			),
			'Girafabot' => array(
				'Mozilla/4.0 (compatible; MSIE 5.0; Windows NT; Girafabot; girafabot at girafa dot com; http://www.girafa.com)' => 'Girafabot',
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 4.0; Girafabot; girafabot at girafa dot com; http://www.girafa.com)' => 'Girafabot b',
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; SV1; .NET CLR 1.1.4322; Girafabot [girafa.com])' => 'Girafabot c',
			),
			'gocrawl' => array(
				'Googlebot (gocrawl v0.4)' => 'gocrawl v0.4',
			),
			'GOFORITBOT' => array(
				'GOFORITBOT ( http://www.goforit.com/about/ )' => 'GOFORITBOT',
			),
			'gonzo' => array(
				'gonzo2[P] +http://www.suchen.de/faq.html' => 'gonzo2',
				'gonzo1[P] +http://www.suchen.de/faq.html' => 'gonzo1',
				'gonzo/1[P] (+http://www.suchen.de/faq.html)' => 'gonzo/1',
				'gonzo2[p] (+http://www.suchen.de/faq.html)' => 'gonzo2',
			),
			'Googlebot' => array(
				'Googlebot-Image/1.0' => 'Googlebot-Image/1.0',
				'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)' => 'Googlebot/2.1',
				'Mediapartners-Google' => 'Mediapartners-Google',
				'AdsBot-Google-Mobile (+http://www.google.com/mobile/adsbot.html) Mozilla (iPhone; U; CPU iPhone OS 3 0 like Mac OS X) AppleWebKit (KHTML, like Gecko) Mobile Safari' => 'AdsBot-Google-Mobile',
				'Googlebot-Video/1.0' => 'Googlebot-Video/1.0',
				'Googlebot/2.1 (+http://www.google.com/bot.html)' => 'Googlebot/2.1',
				'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/534.51 (KHTML, like Gecko; Google Web Preview) Chrome/12.0.742 Safari/534.51' => 'Google Web Preview',
				'Mozilla/5.0 (en-us) AppleWebKit/525.13 (KHTML, like Gecko; Google Web Preview) Version/3.1 Safari/525.13' => 'Google Web Preview',
				'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/534.24 (KHTML, like Gecko; Google Web Preview) Chrome/11.0.696 Safari/534.24 ' => 'Google Web Preview',
				'SAMSUNG-SGH-E250/1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 UP.Browser/6.2.3.3.c.1.101 (GUI) MMP/2.0 (compatible; Googlebot-Mobile/2.1; +http://www.google.com/bot.html)' => 'Googlebot-Mobile',
				'DoCoMo/2.0 N905i(c100;TB;W24H16) (compatible; Googlebot-Mobile/2.1; +http://www.google.com/bot.html)' => 'Googlebot-Mobile',
				'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_1 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8B117 Safari/6531.22.7 (compatible; Googlebot-Mobile/2.1; +http://www.google.com/bot.html)' => 'Googlebot-Mobile/2.1',
				'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.4 (KHTML, like Gecko; Google Web Preview) Chrome/22.0.1229 Safari/537.4' => 'Google Web Preview',
				'Mozilla/5.0 (Windows NT 6.1; rv:6.0) Gecko/20110814 Firefox/6.0 Google (+https://developers.google.com/+/web/snippet/)' => 'Googlebot snippet',
				'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25 (compatible; Googlebot-Mobile/2.1; +http://www.google.com/bot.html)' => 'Googlebot-Mobile',
				'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google Web Preview) Chrome/27.0.1453 Safari/537.36' => 'Google Web Preview',
				'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)' => 'Googlebot-Mobile',
			),
			'Grahambot' => array(
				'Grahambot/0.1 (+http://www.sunaga-lab.com/graham-bot)' => 'Grahambot/0.1',
			),
			'GrapeshotCrawler' => array(
				'Mozilla/5.0 (compatible; GrapeshotCrawler/2.0; +http://www.grapeshot.co.uk/crawler.php)' => 'GrapeshotCrawler/2.0',
				'Mozilla/5.0 (compatible; grapeFX/0.9; crawler@grapeshot.co.uk' => 'grapeFX/0.9',
			),
			'GurujiBot' => array(
				'Mozilla/5.0 (compatible; GurujiBot/1.0; +http://www.guruji.com/en/WebmasterFAQ.html)' => 'GurujiBot/1.0',
			),
			'Hailoobot' => array(
				'Mozilla/5.0 (compatible; Hailoobot/1.2; +http://www.hailoo.com/spider.html)' => 'Hailoobot/1.2',
			),
			'HatenaScreenshot' => array(
				'HatenaScreenshot/1.0 (checker)' => 'HatenaScreenshot/1.0 (checker)',
			),
			'hawkReader' => array(
				'hawkReader/1.8 (Link Parser; http://www.hawkreader.com/; Allow like Gecko) Build/f2b2566' => 'hawkReader/1.8',
			),
			'HeartRails_Capture' => array(
				'Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.8.1.20) Gecko/20090429 HeartRails_Capture/0.6 (+http://capture.heartrails.com/) BonEcho/2.0.0.20' => 'HeartRails_Capture/0.6',
			),
			'heritrix' => array(
				'Mozilla/5.0 (compatible; heritrix/1.12.1 +http://www.webarchiv.cz)' => 'heritrix/1.12.1',
				'Mozilla/5.0 (compatible; heritrix/1.14.3 +http://archive.org)' => 'heritrix/1.14.3',
				'Mozilla/5.0 (compatible; heritrix/2.0.2 +http://seekda.com)' => 'heritrix/2.0.2',
				'Mozilla/5.0 (compatible; heritrix/1.14.2 +http://rjpower.org)' => 'heritrix/1.14.2',
				'Mozilla/5.0 (compatible; heritrix/2.0.2 +http://aihit.com)' => 'heritrix/2.0.2',
				'Mozilla/5.0 (compatible; heritrix/1.12.1b +http://netarkivet.dk/website/info.html)' => 'heritrix/1.12.1b',
				'Mozilla/5.0 (compatible; heritrix/1.14.3 +http://www.webarchiv.cz)' => 'heritrix/1.14.3',
				'Mozilla/5.0 (compatible; heritrix/1.14.3.r6601 +http://www.buddybuzz.net/yptrino)' => 'heritrix/1.14.3.r6601',
				'Mozilla/5.0 (compatible; heritrix/3.0.0-SNAPSHOT-20091120.021634 +http://crawler.archive.org)' => 'heritrix/3.0.0',
				'Mozilla/5.0 (compatible; heritrix/1.14.2 +http://www.webarchiv.cz)' => 'heritrix/1.14.2',
				'Mozilla/5.0 (compatible; heritrix/3.1.1-SNAPSHOT-20120116.200628 +http://www.archive.org/details/archive.org_bot)' => 'heritrix/3.1.1',
			),
			'HiddenMarket' => array(
				'HiddenMarket-1.0-beta (www.hiddenmarket.net/crawler.php)' => 'HiddenMarket-1.0-beta',
			),
			'Holmes' => array(
				'holmes/3.12.4 (http://morfeo.centrum.cz/bot)' => 'holmes/3.12.4 - morfeo',
				'holmes/3.11 (http://morfeo.centrum.cz/bot)' => 'holmes/3.11',
				'holmes/3.9 (onet.pl)' => 'holmes/3.9 - onet.pl',
				'holmes/3.9 (OnetSzukaj/5.0; +http://szukaj.onet.pl)' => 'holmes/3.9 - onet.pl b',
				'holmes/3.10.1 (OnetSzukaj/5.0; +http://szukaj.onet.pl)' => 'holmes/3.10.1 - onet.pl',
				'holmes/3.11 (OnetSzukaj/5.0; +http://szukaj.onet.pl)' => 'holmes/3.11 - onet.pl',
			),
			'HolmesBot' => array(
				'HolmesBot (http://holmes.ge)' => 'HolmesBot',
			),
			'HomeTags' => array(
				'Mozilla/5.0 (compatible; HomeTags/1.0; +http://www.hometags.nl/bot)' => 'HomeTags/1.0',
				'Mozilla/5.0 (compatible; HomeTags/1.0;  http://www.hometags.nl/bot)' => 'HomeTags/1.0',
			),
			'HooWWWer' => array(
				'HooWWWer/2.1.3 (debugging run) (+http://cosco.hiit.fi/search/hoowwwer/ | mailto:crawler-info<at>hiit.fi)' => 'HooWWWer/2.1.3',
				'HooWWWer/2.2.0 (debugging run) (+http://cosco.hiit.fi/search/hoowwwer/ | mailto:crawler-info<at>hiit.fi)' => 'HooWWWer/2.2.0',
			),
			'HostTracker' => array(
				'Mozilla/4.0 (compatible;HostTracker/2.0;+http://www.host-tracker.com/)' => 'HostTracker/2.0',
			),
			'HostTracker.com' => array(
				'Mozilla/4.0 (compatible; HostTracker.com/1.0;+http://host-tracker.com/)' => 'HostTracker.com/1.0',
			),
			'ht://Dig' => array(
				'cinetic_htdig' => 'ht://Dig',
			),
			'HuaweiSymantecSpider' => array(
				'HuaweiSymantecSpider/1.0+DSE-support@huaweisymantec.com+(compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR ; http://www.huaweisymantec.com/en/IRL/spider)' => 'HuaweiSymantecSpider/1.0',
			),
			'HubSpot Connect' => array(
				'HubSpot Connect 1.0 (http://dev.hubspot.com/)' => 'HubSpot Connect 1.0',
			),
			'HubSpot Crawler' => array(
				'HubSpot Crawler 1.0 http://www.hubspot.com/' => 'HubSpot Crawler 1.0',
			),
			'HypeStat' => array(
				'Mozilla/5.0 (compatible; hypestat/1.0; +http://www.hypestat.com/bot)' => 'HypeStat/1.0',
			),
			'iaskspider' => array(
				'iaskspider/2.0(+http://iask.com/help/help_index.html)' => 'iaskspider/2.0',
			),
			'ia_archiver' => array(
				'ia_archiver-web.archive.org' => 'ia_archiver',
				'ia_archiver (+http://www.alexa.com/site/help/webmasters; crawler@alexa.com)' => 'ia_archiver alexa',
			),
			'ICC-Crawler' => array(
				'ICC-Crawler(Mozilla-compatible; ; http://kc.nict.go.jp/project1/crawl.html)' => 'ICC-Crawler',
				'ICC-Crawler/2.0 (Mozilla-compatible; ; http://kc.nict.go.jp/project1/crawl.html)' => 'ICC-Crawler/2.0',
			),
			'ICF_Site_Crawler' => array(
				'ICF_Site_Crawler+(see+http://www.infocenter.fi/spiderinfo.html)' => 'ICF_Site_Crawler',
			),
			'ichiro' => array(
				'ichiro/1.0 (ichiro@nttr.co.jp)' => 'ichiro/1.0',
				'ichiro/2.0 (ichiro@nttr.co.jp)' => 'ichiro/2.0',
				'ichiro/2.0 (http://help.goo.ne.jp/door/crawler.html)' => 'ichiro/2.0',
				'ichiro/2.01 (http://help.goo.ne.jp/door/crawler.html)' => 'ichiro/2.01',
				'ichiro/3.0 (http://help.goo.ne.jp/door/crawler.html)' => 'ichiro/3.0',
				'ichiro/4.0 (http://help.goo.ne.jp/door/crawler.html)' => 'ichiro/4.0',
				'ichiro/5.0 (http://help.goo.ne.jp/door/crawler.html)' => 'ichiro/5.0',
				'ichiro/3.0 (http://help.goo.ne.jp/help/article/1142)' => 'ichiro/3.0',
				'ichiro/3.0 (http://search.goo.ne.jp/option/use/sub4/sub4-1/)' => 'ichiro/3.0',
			),
			'iCjobs' => array(
				'Mozilla/5.0 (X11; U; Linux i686; de; rv:1.9.0.1; compatible; iCjobs Stellenangebote Jobs; http://www.icjobs.de) Gecko/20100401 iCjobs/3.2.3' => 'iCjobs/3.2.3',
			),
			'IdeelaborPlagiaat' => array(
				'IdeelaborPlagiaat/1' => 'IdeelaborPlagiaat/1',
			),
			'idmarch' => array(
				'Mozilla/5.0 (compatible; idmarch Automatic.beta/1.2; +http://www.idmarch.org/bot.html)' => 'idmarch Automatic.beta/1.2',
			),
			'Iframely' => array(
				'Iframely/0.6.0 (+http://iframely.com/;)' => 'Iframely/0.6.0',
			),
			'IlseBot' => array(
				'IlseBot/1.1' => 'IlseBot/1.1',
			),
			'IlTrovatore' => array(
				'IlTrovatore/1.2 (IlTrovatore; http://www.iltrovatore.it/bot.html; bot@iltrovatore.it)' => 'IlTrovatore/1.2',
			),
			'IlTrovatore-Setaccio' => array(
				'IlTrovatore-Setaccio/1.2 (It search engine; http://www.iltrovatore.it/bot.html; bot@iltrovatore.it)' => 'IlTrovatore-Setaccio/1.2',
				'IlTrovatore-Setaccio/1.2 (Italy search engine; http://www.iltrovatore.it/bot.html; bot@iltrovatore.it)' => 'IlTrovatore-Setaccio/1.2 b',
			),
			'imbot' => array(
				'Mozilla/5.0 (compatible; imbot/0.1 +http://internetmemory.org/en/)' => 'imbot/0.1',
			),
			'immediatenet thumbnails' => array(
				'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535. Safari/535.22+; (+http://immediatenet.com/thumbnail_api.html; free thumbnails)' => 'immediatenet thumbnails',
			),
			'ImplisenseBot' => array(
				'ImplisenseBot 1.0' => 'ImplisenseBot 1.0',
			),
			'Influencebot' => array(
				'Influencebot/0.9; (Automatic classification of websites; http://www.influencebox.com/; info@influencebox.com)' => 'Influencebot/0.9',
			),
			'InfociousBot' => array(
				'InfociousBot (+http://corp.infocious.com/tech_crawler.php)' => 'InfociousBot b',
			),
			'Infohelfer' => array(
				'infohelfer/1.1.0 (http://www.infohelfer.de/)' => 'Infohelfer/1.0',
				'Mozilla/5.0 (compatible; Infohelfer/1.2.0; +http://www.infohelfer.de/crawler.php)' => 'Infohelfer/1.2.0',
				'Mozilla/5.0 (compatible; Infohelfer/1.3.0; +http://www.infohelfer.de/crawler.php)' => 'Infohelfer/1.3.0',
				'Mozilla/5.0 (compatible; Infohelfer/1.3.3; +http://www.infohelfer.de/crawler.php)' => 'Infohelfer/1.3.3',
				'Mozilla/5.0 (compatible; Infohelfer/1.4.3; +http://www.infohelfer.de/crawler.php)' => 'Infohelfer/1.4.3',
			),
			'IntegromeDB' => array(
				'www.integromedb.org/Crawler' => 'IntegromeDB',
			),
			'InternetSeer' => array(
				'InternetSeer.com' => 'InternetSeer (Web Monitor)',
			),
			'Ipselonbot' => array(
				'Ipselonbot/0.47-beta (Ipselon; http://www.ipselon.com/intl/en/ipselonbot.html; ipselonbot@ipselon.com)' => 'Ipselonbot/0.47-beta',
				'Ipselonbot/1.0-beta (+; http://www.ipselon.com/intl/en/ipselonbot.html)' => 'Ipselonbot/1.0-beta',
			),
			'IRLbot' => array(
				'IRLbot/2.0 (+http://irl.cs.tamu.edu/crawler)' => 'IRLbot/2.0',
				'IRLbot/2.0 (compatible; MSIE 6.0; http://irl.cs.tamu.edu/crawler)' => 'IRLbot/2.0 b',
				'IRLbot/3.0 (compatible; MSIE 6.0; http://irl.cs.tamu.edu/crawler)' => 'IRLbot/3.0',
				'IRLbot/3.0 (compatible; MSIE 6.0; http://irl.cs.tamu.edu/crawler/)' => 'IRLbot/3.0 b',
			),
			'IstellaBot' => array(
				'Mozilla/5.0 (compatible; IstellaBot/1.01.18 +http://www.tiscali.it/)' => 'IstellaBot/1.01.18',
				'Mozilla/5.0 (compatible; IstellaBot/1.10.2 +http://www.tiscali.it/)' => 'IstellaBot/1.10.2',
				'Mozilla/5.0 (compatible; IstellaBot/1.18.81 +http://www.tiscali.it/)' => 'IstellaBot/1.18.81',
			),
			'IXEbot' => array(
				'Mozilla/5.0 (compatible; IXEbot; +http://medialab.di.unipi.it/IXEbot.html)' => 'IXEbot',
			),
			'Jabse.com Crawler' => array(
				'Jabse.com Crawler v.1.0 www.jabse.com/crawler.php//imagecrawler' => 'Jabse.com Crawler v.1.0',
			),
			'JadynAve' => array(
				'JadynAve - http://www.jadynave.com/robot' => 'JadynAve',
			),
			'JadynAveBot' => array(
				'Mozilla/5.0 (compatible; JadynAveBot; +http://www.jadynave.com/robot)' => 'JadynAveBot',
			),
			'Jambot' => array(
				'Jambot/0.1.1 (Jambot; http://www.jambot.com/blog; crawler@jambot.com)' => 'Jambot/0.1.1',
			),
			'JikeSpider' => array(
				'JikeSpider Mozilla/5.0 (compatible; JikeSpider; +http://shoulu.jike.com/spider.html)' => 'JikeSpider',
				'Mozilla/5.0 (compatible; JikeSpider; +http://shoulu.jike.com/spider.html)' => 'JikeSpider b',
			),
			'Job Roboter Spider' => array(
				'Mozilla/5.0 (compatible;WI Job Roboter Spider Version 3;+http://www.webintegration.at)' => 'Job Roboter Spider 3',
			),
			'JUST-CRAWLER' => array(
				'JUST-CRAWLER(+http://www.justsystems.com/jp/tech/crawler/)' => 'JUST-CRAWLER',
			),
			'Jyxobot' => array(
				'Jyxobot/1' => 'Jyxobot',
				'JyxobotRSS/0.06' => 'JyxobotRSS/0.06',
			),
			'Kakle Bot' => array(
				'kakle-spider/0.1 (kakle-spider; http://www.kakle.com/bot.html; support@kakle.com)' => 'kakle-spider/0.1',
			),
			'Kalooga' => array(
				'kalooga/KaloogaBot (Kalooga; http://www.kalooga.com/info.html?page=crawler)' => 'Kalooga',
				'kalooga/KaloogaBot (Kalooga; http://www.kalooga.com/info.html?page=crawler; crawler@kalooga.com)' => 'Kalooga',
			),
			'Karneval-Bot' => array(
				'Karneval-Bot (Version: 1.06, powered by www.karnevalsuchmaschine.de +http://www.karnevalsuchmaschine.de/bot.html)' => 'Karneval-Bot/1.06',
			),
			'KeywenBot' => array(
				'KeywenBot/4.1  http://www.keywen.com/Encyclopedia/Links' => 'KeywenBot/4.1',
			),
			'KeywordDensityRobot' => array(
				'KeywordDensityRobot/0.8 (http://www.seocentro.com/tools/search-engines/keyword-density.html)' => 'KeywordDensityRobot/0.8',
			),
			'KomodiaBot' => array(
				'Mozilla/5.0 (Windows NT 6.1; Win64; x64) KomodiaBot/1.0' => 'KomodiaBot/1.0',
			),
			'Kongulo' => array(
				'Kongulo v0.1 personal web crawler' => 'Kongulo v0.1',
			),
			'Kraken' => array(
				'Mozilla/5.0 (compatible; Kraken/0.1; http://linkfluence.net/; bot@linkfluence.net)' => 'Kraken/0.1',
			),
			'KRetrieve' => array(
				'KRetrieve/1.1/dbsearchexpert.com' => 'KRetrieve/1.1',
			),
			'KrOWLer' => array(
				'KrOWLer/0.0.1, matentzn at cs dot man dot ac dot uk' => 'KrOWLer/0.0.1',
				'KrOWLer/0.1.0, matentzn at cs dot man dot ac dot uk' => 'KrOWLer/0.1.0',
			),
			'Krugle' => array(
				'Krugle/Krugle,Nutch/0.8+ (Krugle web crawler; http://www.krugle.com/crawler/info.html; webcrawler@krugle.com)' => 'Krugle (Nutch/0.8+)',
				'Krugle/Krugle,Nutch/0.8+ (Krugle web crawler; http://corp.krugle.com/crawler/info.html; webcrawler@krugle.com)' => 'Krugle (Nutch/0.8+) b',
			),
			'ksibot' => array(
				'ksibot/5.2m (+http://ego.ms.mff.cuni.cz/)' => 'ksibot/5.2m',
				'ksibot/7.0d (+http://ego.ms.mff.cuni.cz/)' => 'ksibot/7.0d',
				'ksibot/8.0a (+http://ego.ms.mff.cuni.cz/)' => 'ksibot/8.0a',
			),
			'kulturarw' => array(
				'Mozilla/5.0 (compatible; kulturarw3 +http://www.kb.se/om/projekt/Svenska-webbsidor---Kulturarw3/)' => 'kulturarw3',
			),
			'L.webis' => array(
				'L.webis/0.50 (http://webalgo.iit.cnr.it/index.php?pg=lwebis)' => 'L.webis/0.50',
				'L.webis/0.51 (http://webalgo.iit.cnr.it/index.php?pg=lwebis)' => 'L.webis/0.51',
				'L.webis/0.53 (http://webalgo.iit.cnr.it/index.php?pg=lwebis)' => 'L.webis/0.53',
				'L.webis/0.44 (http://webalgo.iit.cnr.it/index.php?pg=lwebis)' => 'L.webis/0.44',
			),
			'LapozzBot' => array(
				'LapozzBot/1.4 (+http://robot.lapozz.hu)' => 'LapozzBot/1.4 hu',
				'LapozzBot/1.4 (+http://robot.lapozz.com)' => 'LapozzBot/1.4 com',
				'LapozzBot/1.5 (+http://robot.lapozz.hu) ' => 'LapozzBot/1.5',
			),
			'Larbin' => array(
				'Larbin (larbin2.6.3@unspecified.mail)' => 'Larbin/2.6.3',
			),
			'Leikibot' => array(
				'Leikibot/1.0 (+http://www.leiki.com)' => 'Leikibot/1.0',
			),
			'LemurWebCrawler' => array(
				'LarbinWebCrawler (spider@download11.com)' => 'LabrinWebCrawler',
				'The Lemur Web Crawler/Nutch-1.3 (Lemur Web Crawler; http://boston.lti.cs.cmu.edu/crawler_12/; admin@lemurproject.org)' => 'LemurWebCrawler',
				'Mozilla/5.0 (compatible; heritrix/3.1.0-RC1 +http://boston.lti.cs.cmu.edu/crawler_12/)' => 'LemurWebCrawler',
			),
			'LexxeBot' => array(
				'LexxeBot/1.0 (lexxebot@lexxe.com)' => 'LexxeBot/1.0',
			),
			'Lijit' => array(
				'Lijit Crawler (+http://www.lijit.com/robot/crawler)' => 'Lijit',
			),
			'LinguaBot' => array(
				'LinguaBot/v0.001-dev (MultiLinual Sarch Engine v0.001; LinguaSeek; admin at linguaseek dot com)' => 'LinguaBot/v0.001-dev',
			),
			'linguatools' => array(
				'linguatools-bot/Nutch-1.6 (searching for translated pages; http://www.linguatools.de/linguatoolsbot.html; peter dot kolb at linguatools dot org)' => 'linguatools',
			),
			'Linguee Bot' => array(
				'Linguee Bot (http://www.linguee.com/bot)' => 'Linguee Bot',
				'Linguee Bot (http://www.linguee.com/bot; bot@linguee.com)' => 'Linguee Bot',
			),
			'Link Valet Online' => array(
				'Link Valet Online 1.1' => 'Link Valet Online 1.1',
				'Link Valet Online 1.2' => 'Link Valet Online 1.2',
			),
			'LinkAider' => array(
				'LinkAider (http://linkaider.com/crawler/)' => 'LinkAider',
			),
			'linkdex.com' => array(
				'linkdex.com/v2.0' => 'linkdex.com/v2.0',
			),
			'linkdexbot' => array(
				'linkdexbot/Nutch-1.0-dev (http://www.linkdex.com/; crawl at linkdex dot com)' => 'linkdexbot',
				'Mozilla/5.0 (compatible; linkdexbot/2.0; +http://www.linkdex.com/about/bots/)' => 'linkdexbot/2.0',
				'Mozilla/5.0 (compatible; linkdexbot/2.1; +http://www.linkdex.com/about/bots/)' => 'linkdexbot/2.1',
				'Mozilla/5.0 (compatible; linkdexbot/2.0; +http://www.linkdex.com/bots/)' => 'linkdexbot/2.0',
				'Mozilla/5.0 (compatible; linkdexbot/2.1; +http://www.linkdex.com/bots/)' => 'linkdexbot/2.1',
			),
			'LinkedInBot' => array(
				'LinkedInBot/1.0 (compatible; Mozilla/5.0; Jakarta Commons-HttpClient/3.1 +http://www.linkedin.com)' => 'LinkedInBot/1.0',
			),
			'linksmanager_bot' => array(
				'Mozilla/5.0 (compatible; LinksManager.com_bot +http://linksmanager.com/linkchecker.html)' => 'linksmanager_bot',
			),
			'LinkWalker' => array(
				'LinkWalker' => 'LinkWalker',
				'LinkWalker/2.0' => 'LinkWalker/2.0',
			),
			'Lipperhey Spider' => array(
				'Mozilla/5.0 (compatible; Lipperhey SEO Service; http://www.lipperhey.com/)' => 'Lipperhey Spider',
			),
			'livedoor ScreenShot' => array(
				'livedoor ScreenShot/0.10' => 'livedoor ScreenShot/0.10',
			),
			'lmspider' => array(
				'lmspider (lmspider@scansoft.com)' => 'lmspider',
				'lmspider/Nutch-0.9-dev (For research purposes.; www.nuance.com)' => 'lmspider b',
			),
			'LoadImpactPageAnalyzer' => array(
				'LoadImpactPageAnalyzer/1.3.0 (Load Impact; http://loadimpact.com/)' => 'LoadImpactPageAnalyzer/1.3.0',
			),
			'LoadTimeBot' => array(
				'Mozilla/5.0 (compatible; LoadTimeBot/0.7; +http://www.load-time.com/bot.html)' => 'LoadTimeBot/0.7',
				'Mozilla/5.0 (compatible; LoadTimeBot/0.81; +http://www.load-time.com/bot.html)' => 'LoadTimeBot/0.81',
				'Mozilla/5.0 (compatible; LoadTimeBot/0.9; +http://www.loadtime.net/bot.html)' => 'LoadTimeBot/0.9',
			),
			'LuminateBot' => array(
				'Mozilla/5.0 (compatible; LuminateBot/1.0; +http://www.luminate.com/bot/)' => 'LuminateBot/1.0',
			),
			'magpie-crawler' => array(
				'magpie-crawler/1.1 (U; Linux amd64; en-GB; +http://www.brandwatch.net)' => 'magpie-crawler/1.1',
			),
			'Mahiti Crawler' => array(
				'Mahiti.Com/Mahiti Crawler-1.0 (Mahiti.Com; http://mahiti.com ; mahiti.com)' => 'Mahiti Crawler-1.0',
			),
			'Mail.Ru bot' => array(
				'Mail.Ru/1.0' => 'Mail.Ru/1.0',
				'Mail.RU/2.0' => 'Mail.RU/2.0',
				'Mozilla/5.0 (compatible; Mail.RU/2.0)' => 'Mail.RU/2.0',
				'Mozilla/5.0 (compatible; Mail.RU_Bot/2.0)' => 'Mail.RU_Bot/2.0',
				'Mozilla/5.0 (compatible; Mail.RU_Bot/2.0; +http://go.mail.ru/help/robots)' => 'Mail.RU_Bot/2.0',
				'Mozilla/5.0 (compatible; Linux x86_64; Mail.RU_Bot/2.0; +http://go.mail.ru/help/robots)' => 'Mail.RU_Bot/2.0',
				'Mozilla/5.0 (compatible; Linux x86_64; Mail.RU_Bot/Img/2.0; +http://go.mail.ru/help/robots)' => 'Mail.RU_Bot/Img/2.0',
				'Mozilla/5.0 (compatible; Linux x86_64; Mail.RU_Bot/Fast/2.0; +http://go.mail.ru/help/robots)' => 'Mail.RU_Bot/Fast/2.0',
				'Mozilla/5.0 (compatible; Linux x86_64; Mail.RU_Bot/Robots/2.0; +http://go.mail.ru/help/robots)' => 'Mail.RU_Bot/Robots/2.0',
			),
			'meanpathbot' => array(
				'Mozilla/5.0 (compatible; meanpathbot/1.0; +http://www.meanpath.com/meanpathbot.html)' => 'meanpathbot/1.0',
			),
			'Megatext' => array(
				'Megatext/Nutch-0.8.1 (Beta; http://www.megatext.cz/; microton@microton.cz)' => 'Megatext-0.8.1 beta',
				'Megatext/Megatext-0.5 (beta; http://www.megatext.cz/; microton@microton.cz)' => 'Megatext-0.5 beta',
			),
			'MeMoNewsBot' => array(
				'MeMoNewsBot/2.0 (http://www.memonews.com/en/crawler)' => 'MeMoNewsBot/2.0',
			),
			'MetaGeneratorCrawler' => array(
				'MetaGeneratorCrawler/1.1 (www.metagenerator.info)' => 'MetaGeneratorCrawler/1.1',
				'MetaGeneratorCrawler/1.3.3 (www.metagenerator.info)' => 'MetaGeneratorCrawler/1.3.3',
				'MetaGeneratorCrawler/1.3.14 (www.metagenerator.info)' => 'MetaGeneratorCrawler/1.3.14',
				'MetaGeneratorCrawler/1.3.4 (www.metagenerator.info)' => 'MetaGeneratorCrawler/1.3.4',
				'MetaGeneratorCrawler/1.3.2 (www.metagenerator.info)' => 'MetaGeneratorCrawler/1.3.2',
				'MetaGeneratorCrawler/1.3.9 (www.metagenerator.info)' => 'MetaGeneratorCrawler/1.3.9',
			),
			'MetaHeadersBot' => array(
				'MetaHeadersBot (+http://www.metaheaders.com/bot.html)' => 'MetaHeadersBot',
			),
			'MetaJobBot' => array(
				'Mozilla/5.0 (compatible; MetaJobBot; http://www.metajob.at/crawler)' => 'MetaJobBot',
			),
			'MetamojiCrawler' => array(
				'Mozilla/5.0 (compatible; MetamojiCrawler/1.0; +http://www.metamoji.com/jp/crawler.html' => 'MetamojiCrawler/1.0',
			),
			'Metaspinner/0.01' => array(
				'Metaspinner/0.01 (Metaspinner; http://www.meta-spinner.de/; support@meta-spinner.de/)' => 'Metaspinner/0.01',
			),
			'MetaTagRobot' => array(
				'MetaTagRobot/1.6 (http://www.widexl.com/remote/search-engines/metatag-analyzer.html)' => 'MetaTagRobot/1.6',
				'MetaTagRobot/2.1 (http://www.widexl.com/remote/search-engines/metatag-analyzer.html)' => 'MetaTagRobot/2.1',
			),
			'MetaURI' => array(
				'MetaURI API +metauri.com' => 'MetaURI',
			),
			'MetaURI API' => array(
				'MetaURI API/2.0 +metauri.com' => 'MetaURI API/2.0',
			),
			'MIA Bot' => array(
				'MIA DEV/search:robot/0.0.1 (This is the MIA Bot - crawling for mia research project. If you feel unhappy and do not want to be visited by our crawler send an email to spider@neofonie.de; http://spider.neofonie.de; spider@neofonie.de)' => 'MIA Bot',
			),
			'MiaDev' => array(
				'MiaDev/0.0.1 (MIA Bot for research project MIA (www.MIA-marktplatz.de); http://www.mia-marktplatz.de/spider; spider@mia-marktplatz.de)' => 'MiaDev/0.0.1',
			),
			'miniRank' => array(
				'miniRank/1.2 (miniRank; http://minirank.com/; MiniRank)' => 'miniRank/1.2',
				'miniRank/1.5 (miniRank; www.minirank.com; robot)' => 'miniRank/1.5',
				'miniRank/1.6 (Website ranking; www.minirank.com; robot)' => 'miniRank/1.6',
				'miniRank/2.0 (miniRank; http://minirank.com/; website ranking engine)' => 'miniRank/2.0',
				'miniRank/3.1 (miniRank; www.minirank.com; website ranking engine)' => 'miniRank/3.1',
			),
			'MixBot' => array(
				'MixBot (+http://t.co/GSRLLKex24)' => 'MixBot',
			),
			'MJ12bot' => array(
				'Mozilla/5.0 (compatible; MJ12bot/v1.2.5; http://www.majestic12.co.uk/bot.php?+)' => 'MJ12bot/v1.2.5',
				'Mozilla/5.0 (compatible; MJ12bot/v1.2.4; http://www.majestic12.co.uk/bot.php?+)' => 'MJ12bot/v1.2.4',
				'Mozilla/5.0 (compatible; MJ12bot/v1.2.1; http://www.majestic12.co.uk/bot.php?+)' => 'MJ12bot/v1.2.1',
				'Mozilla/5.0 (compatible; MJ12bot/v1.2.3; http://www.majestic12.co.uk/bot.php?+)' => 'MJ12bot/v1.2.3',
				'Mozilla/5.0 (compatible; MJ12bot/v1.3.0; http://www.majestic12.co.uk/bot.php?+)' => 'MJ12bot/v1.3.0',
				'Mozilla/5.0 (compatible; MJ12bot/v1.3.1; http://www.majestic12.co.uk/bot.php?+)' => 'MJ12bot/v1.3.1',
				'Mozilla/5.0 (compatible; MJ12bot/v1.3.2; http://www.majestic12.co.uk/bot.php?+)' => 'MJ12bot/v1.3.2',
				'Mozilla/5.0 (compatible; MJ12bot/v1.3.3; http://www.majestic12.co.uk/bot.php?+)' => 'MJ12bot/v1.3.3',
				'Mozilla/5.0 (compatible; MJ12bot/v1.4.0; http://www.majestic12.co.uk/bot.php?+)' => 'MJ12bot/v1.4.0',
				'Mozilla/5.0 (compatible; MJ12bot/v1.4.1; http://www.majestic12.co.uk/bot.php?+)' => 'MJ12bot/v1.4.1',
				'Mozilla/5.0 (compatible; MJ12bot/v1.4.2; http://www.majestic12.co.uk/bot.php?+)' => 'MJ12bot/v1.4.2',
				'Mozilla/5.0 (compatible; MJ12bot/v1.4.3; http://www.majestic12.co.uk/bot.php?+)' => 'MJ12bot/v1.4.3',
				'Mozilla/5.0 (compatible; MJ12bot/v1.4.4; http://www.majestic12.co.uk/bot.php?+)' => 'MJ12bot/v1.4.4',
				'Mozilla/5.0 (compatible; MJ12bot/v1.4.5; http://www.majestic12.co.uk/bot.php?+)' => 'MJ12bot/v1.4.5',
			),
			'MLBot' => array(
				'MLBot (www.metadatalabs.com)' => 'MLBot',
				'MLBot (www.metadatalabs.com/mlbot)' => 'MLBot b',
			),
			'MnoGoSearch' => array(
				'MnoGoSearch/3.3.2' => 'MnoGoSearch/3.3.2',
				'MnoGoSearch/3.3.6' => 'MnoGoSearch/3.3.6',
				'MnoGoSearch/3.3.9' => 'MnoGoSearch/3.3.9',
				'MnoGoSearch/3.2.37' => 'MnoGoSearch/3.2.37',
			),
			'Moatbot' => array(
				'Mozilla/5.0 (compatible; Moatbot/2.2; +http://www.moat.com/pages/moatbot)' => 'Moatbot/2.2',
			),
			'moba-crawler' => array(
				'DoCoMo/2.0 N902iS(c100;TB;W24H12)(compatible; moba-crawler; http://crawler.dena.jp/)' => 'moba-crawler',
			),
			'MojeekBot' => array(
				'MojeekBot/0.2 (archi; http://www.mojeek.com/bot.html)' => 'MojeekBot/0.2',
				'Mozilla/5.0 (compatible; MojeekBot/0.2; http://www.mojeek.com/bot.html#relaunch)' => 'MojeekBot/0.2 Relaunch',
				'Mozilla/5.0 (compatible; MojeekBot/0.2; http://www.mojeek.com/bot.html)' => 'MojeekBot/0.2',
				'Mozilla/5.0 (compatible; MojeekBot/0.5; http://www.mojeek.com/bot.html)' => 'MojeekBot/0.5',
				'Mozilla/5.0 (compatible; MojeekBot/0.6; http://www.mojeek.com/bot.html)' => 'MojeekBot/0.6',
			),
			'Motoricerca-Robots.txt-Checker' => array(
				'Motoricerca-Robots.txt-Checker/1.0 (http://tool.motoricerca.info/robots-checker.phtml)' => 'Motoricerca-Robots.txt-Checker/1.0',
			),
			'mozDex' => array(
				'Mozdex/0.7.2-dev (Mozdex; http://www.mozdex.com/bot.html; spider@mozdex.com)' => 'Mozdex/0.7.2-dev',
				'Mozdex/0.7.2 (Mozdex; http://www.mozdex.com/bot.html; spider@mozdex.com)' => 'Mozdex/0.7.2',
				'Mozdex/0.7.1 (Mozdex; http://www.mozdex.com/bot.html; spider@mozdex.com)' => 'Mozdex/0.7.1',
			),
			'Mp3Bot' => array(
				'Mozilla/5.0 (compatible; Mp3Bot/0.4; +http://mp3realm.org/mp3bot/)' => 'Mp3Bot/0.4',
				'Mozilla/5.0 (compatible; Mp3Bot/0.7; +http://mp3realm.org/mp3bot/)' => 'Mp3Bot/0.7',
			),
			'MQbot' => array(
				'MQbot metaquerier.cs.uiuc.edu/crawler' => 'MQbot',
				'MQBOT/Nutch-0.9-dev (MQBOT Nutch Crawler; http://falcon.cs.uiuc.edu; mqbot@cs.uiuc.edu)' => 'MQBOT/Nutch-0.9-dev',
				'MQBOT/Nutch-0.9-dev (MQBOT Crawler; http://falcon.cs.uiuc.edu; mqbot@cs.uiuc.edu)' => 'MQBOT/Nutch-0.9-dev b',
				'MQBOT/Nutch-0.9-dev (MQBOT Nutch Crawler; http://vwbot.cs.uiuc.edu; mqbot@cs.uiuc.edu)' => 'MQBOT/Nutch-0.9-dev c',
			),
			'MSNBot' => array(
				'msnbot/1.0 (+http://search.msn.com/msnbot.htm)' => 'MSNBot/1.0',
				'msnbot/2.0b (+http://search.msn.com/msnbot.htm)' => 'MSNBot/2.0b',
				'msnbot/1.1 (+http://search.msn.com/msnbot.htm)' => 'MSNBot/1.1',
				'msnbot-media/1.1 (+http://search.msn.com/msnbot.htm)' => 'msnbot-media/1.1',
				'adidxbot/1.1 (+http://search.msn.com/msnbot.htm)' => 'adidxbot/1.1',
				'msnbot/2.0b (+http://search.msn.com/msnbot.htm).' => 'MSNBot/2.0b + .',
				'msnbot/2.0b (+http://search.msn.com/msnbot.htm)._' => 'MSNBot/2.0b + ._',
				'msnbot-NewsBlogs/2.0b (+http://search.msn.com/msnbot.htm)' => 'msnbot-NewsBlogs/2.0b',
				'msnbot-UDiscovery/2.0b (+http://search.msn.com/msnbot.htm)' => 'msnbot-UDiscovery/2.0b',
				'msnbot-media/2.0b (+http://search.msn.com/msnbot.htm)' => 'msnbot-media/2.0b',
				'adidxbot/2.0 (+http://search.msn.com/msnbot.htm)' => 'adidxbot/2.0',
			),
			'MSRBOT' => array(
				'MSRBOT' => 'MSRBOT',
				'MSRBOT (http://research.microsoft.com/research/sv/msrbot/)' => 'MSRBOT b',
				'MSRBOT (http://research.microsoft.com/research/sv/msrbot)' => 'MSRBOT c',
				'MSRBOT (http://research.microsoft.com/research/sv/msrbot/' => 'MSRBOT d',
			),
			'MultiCrawler' => array(
				'multicrawler (+http://sw.deri.org/2006/04/multicrawler/robots.html)' => 'MultiCrawler',
			),
			'musobot' => array(
				'Mozilla/5.0 (compatible; musobot/1.0; info@muso.com; +http://www.muso.com)' => 'musobot/1.0',
			),
			'MyFamilyBot' => array(
				'Mozilla/4.0 (compatible; MyFamilyBot/1.0; http://www.myfamilyinc.com)' => 'MyFamilyBot/1.0',
				'Mozilla/4.0 (compatible; MyFamilyBot/1.0; http://www.ancestry.com/learn/bot.aspx)' => 'MyFamilyBot/1.0 b',
				'mozilla/4.0 (compatible; myfamilybot/1.0; http://www.ancestry.com/learn/bot.aspx)' => 'MyFamilyBot/1.0',
			),
			'Najdi.si' => array(
				'Mozilla/5.0 (compatible; Najdi.si/3.1)' => 'Najdi.si/3.1',
			),
			'NalezenCzBot' => array(
				'NalezenCzBot/1.0 (http://www.nalezen.cz)' => 'NalezenCzBot/1.0',
				'NalezenCzBot/1.0 (http://www.nalezen.cz/about-crawler)' => 'NalezenCzBot/1.0',
			),
			'NaverBot' => array(
				'Yeti/1.0 (NHN Corp.; http://help.naver.com/robots/)' => 'Yeti/1.0',
				'Mozilla/4.0 (compatible; NaverBot/1.0; http://help.naver.com/customer_webtxt_02.jsp)' => 'NaverBot/1.0',
				'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0_1 like Mac OS X) (compatible; Yeti-Mobile/0.1; +http://help.naver.com/robots/)' => ' Yeti-Mobile/0.1',
				'Yeti-FeedItemCrawler/1.0 (NHN Corp.; http://help.naver.com/robots/)' => 'Yeti-FeedItemCrawler/1.0',
				'Yepi/1.0 (NHN Corp.; http://help.naver.com/robots/)' => 'Yepi/1.0',
				'Yeti/1.1 (NHN Corp.; http://help.naver.com/robots/)' => 'Yeti/1.1',
				'Yeti/1.1 (Naver Corp.; http://help.naver.com/robots/)' => 'Yeti/1.1',
			),
			'navissobot' => array(
				'navissobot/1.7  (+http://navisso.com/)' => 'navissobot/1.7',
			),
			'nekstbot' => array(
				'Nekstbot - http://www.ipipan.waw.pl/nekst/nekstbot/' => 'nekstbot',
				'Mozilla/5.0 (Nekstbot; http://www.ipipan.waw.pl/nekst/nekstbot/)' => 'nekstbot',
			),
			'NerdByNature.Bot' => array(
				'Mozilla/5.0 (compatible; NerdByNature.Bot; http://www.nerdbynature.net/bot)' => 'NerdByNature.Bot',
			),
			'nestReader' => array(
				'nestReader/0.2 (discovery; http://echonest.com/reader.shtml; reader at echonest.com)' => 'nestReader/0.2',
			),
			'NetcraftSurveyAgent' => array(
				'Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)' => 'NetcraftSurveyAgent/1.0',
			),
			'netEstate Crawler' => array(
				'netEstate RSS crawler (+http://www.rss-directory.info/)' => 'netEstate RSS crawler',
				'netEstate NE Crawler (+http://www.sengine.info/)' => 'netEstate NE Crawler',
				'netEstate NE Crawler (+http://www.website-datenbank.de/)' => 'netEstate NE Crawler',
			),
			'Netintelligence LiveAssessment' => array(
				'Netintelligence LiveAssessment - www.netintelligence.com' => 'Netintelligence LiveAssessment',
			),
			'NetResearchServer' => array(
				'nrsbot/5.0(loopip.com/robot.html)' => 'nrsbot/5.0',
				'nrsbot/6.0(loopip.com/robot.html)' => 'nrsbot/6.0',
			),
			'Netseer' => array(
				'Mozilla/5.0 (compatible; Netseer crawler/2.0; +http://www.netseer.com/crawler.html; crawler@netseer.com)' => 'Netseer crawler/2.0',
			),
			'NetWhatCrawler' => array(
				'NetWhatCrawler/0.06-dev (NetWhatCrawler from NetWhat.com; http://www.netwhat.com; support@netwhat.com)' => 'NetWhatCrawler/0.06-dev',
			),
			'NextGenSearchBot' => array(
				'NextGenSearchBot 1 (for information visit http://about.zoominfo.com/About/NextGenSearchBot.aspx)' => 'NextGenSearchBot 1',
				'NextGenSearchBot 1 (for information visit http://www.zoominfo.com/About/misc/NextGenSearchBot.aspx)' => 'NextGenSearchBot 1 b',
			),
			'nextthing.org' => array(
				'Mozilla/5.0 (compatible; nextthing.org/1.0; +http://www.nextthing.org/bot)' => 'nextthing.org/1.0',
			),
			'NG' => array(
				'NG/2.0' => 'NG/2.0',
			),
			'NG-Search' => array(
				'NG-Search/0.90 (NG-SearchBot; http://www.ng-search.com;  )' => 'NG-Search/0.90',
				'NG-Search/0.9.8 (NG-SearchBot; http://www.ng-search.com)' => 'NG-Search/0.9.8',
			),
			'Nigma.ru' => array(
				'Mozilla/5.0 (compatible; Nigma.ru/3.0; crawler@nigma.ru)' => 'Nigma.ru/3.0',
			),
			'NimbleCrawler' => array(
				'Mozilla/5.0 (Windows;) NimbleCrawler 1.14 obeys UserAgent NimbleCrawler For problems contact: crawler@healthline.com' => 'NimbleCrawler/1.14',
				'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.7) NimbleCrawler 1.11 obeys UserAgent NimbleCrawler For problems contact: crawler_at_dataalchemy.com' => 'NimbleCrawler/1.11',
				'Mozilla/5.0 (Windows;) NimbleCrawler 1.12 obeys UserAgent NimbleCrawler For problems contact: crawler@healthline.com' => 'NimbleCrawler/1.12',
				'Mozilla/5.0 (Windows;) NimbleCrawler 1.13 obeys UserAgent NimbleCrawler For problems contact: crawler@healthline.com' => 'NimbleCrawler/1.13',
				'Mozilla/5.0 (Windows;) NimbleCrawler 1.15 obeys UserAgent NimbleCrawler For problems contact: crawler@healthline.com' => 'NimbleCrawler/1.15',
				'Mozilla/5.0 (Windows;) NimbleCrawler 2.0.0 obeys UserAgent NimbleCrawler For problems contact: crawler@healthline.com' => 'NimbleCrawler/2.0.0',
				'Mozilla/5.0 (Windows;) NimbleCrawler 2.0.1 obeys UserAgent NimbleCrawler For problems contact: crawler@healthline.com' => 'NimbleCrawler/2.0.1',
				'Mozilla/5.0 (Windows;) NimbleCrawler 2.0.2 obeys UserAgent NimbleCrawler For problems contact: crawler@healthline.com' => 'NimbleCrawler/2.0.2',
			),
			'NLNZ_IAHarvester2013' => array(
				'Mozilla/5.0 (compatible; NLNZ_IAHarvester2013 +http://natlib.govt.nz/about-us/current-initiatives/web-harvest-2013)' => 'NLNZ_IAHarvester2013',
			),
			'nodestackbot' => array(
				'nodestackbot/0.1 (bot@nodestack.com http://nodestack.com/bot.html)' => 'nodestackbot/0.1',
			),
			'noyona' => array(
				'noyona_0_1' => 'noyona_0_1',
			),
			'NPBot' => array(
				'NPBot/3 (NPBot; http://www.nameprotect.com; npbot@nameprotect.com)' => 'NPBot/3',
			),
			'Nuhk' => array(
				'Nuhk/2.4 ( http://www.neti.ee/cgi-bin/abi/Otsing/Nuhk/)' => 'Nuhk/2.4',
				'Nuhk/2.4 (+http://www.neti.ee/cgi-bin/abi/otsing.html)' => 'Nuhk/2.4 b',
			),
			'NuSearch Spider' => array(
				'NuSearch Spider (compatible; MSIE 6.0)' => 'NuSearch Spider',
				'Nusearch Spider (www.nusearch.com)' => 'NuSearch Spider - b',
			),
			'Nutch' => array(
				'NutchCVS/0.8-dev (Nutch; http://lucene.apache.org/nutch/bot.html; nutch-agent@lucene.apache.org)' => 'NutchCSV/0.8-dev',
				'NutchCVS/0.06-dev (http://www.nutch.org/docs/en/bot.html; rhwarren+nutch@uwaterloo.ca)' => 'NutchCVS/0.06-dev at uwaterloo.ca',
				'NutchCVS/0.7 (Nutch; http://lucene.apache.org/nutch/bot.html; nutch-agent@lucene.apache.org)' => 'NutchCVS/0.7',
				'NutchOSU-VLIB/0.7 (Nutch; http://lucene.apache.org/nutch/bot.html; nutch-agent@lucene.apache.org)' => 'NutchOSU-VLIB/0.7',
				'InternetArchive/0.8-dev (Nutch; http://lucene.apache.org/nutch/bot.html; nutch-agent@lucene.apache.org)' => 'InternetArchive/0.8-dev',
				'NutchCVS/0.7.1 (Nutch; http://lucene.apache.org/nutch/bot.html; nutch-agent@lucene.apache.org)' => 'NutchCVS/0.7.1',
				'NutchCVS/0.7.1 (Nutch; http://www.vvdb.org; voorzitter@vvdb.org)' => 'NutchCVS/0.7.1 vvdg.org',
				'NutchCVS/0.06-dev (Nutch; http://www.nutch.org/docs/en/bot.html; nutch-agent@lists.sourceforge.net)' => 'NutchCVS/0.06-dev',
				'NutchCVS/0.7.1 (Nutch running at UW; http://www.nutch.org/docs/en/bot.html; sycrawl@cs.washington.edu)' => 'NutchCVS/0.7.1 at washihinton.edu',
				'NutchCVS/0.7.1 (Nutch running at UW; http://crawlers.cs.washington.edu/; sycrawl@cs.washington.edu)' => 'NutchCVS/0.7.1 at UW',
				'NutchCVS/0.7.2 (Nutch; http://lucene.apache.org/nutch/bot.html; nutch-agent@lucene.apache.org)' => 'NutchCVS/0.7.2',
				'NutchCVS/0.8-dev (Nutch running at UW; http://www.nutch.org/docs/en/bot.html; sycrawl@cs.washington.edu)' => 'NutchCSV/0.8-dev at UW',
				'asked/Nutch-0.8 (web crawler; http://asked.jp; epicurus at gmail dot com)' => 'Nutch/0.8 at asked.jp',
				'HouxouCrawler/Nutch-0.9-dev (houxou.com\'s nutch-based crawler which serves special interest on-line communities; http://www.houxou.com/crawler; crawler at houxou dot com)' => 'Nutch/0.9-dev at houxou.com',
				'BilgiBetaBot/0.8-dev (bilgi.com (Beta) ; http://lucene.apache.org/nutch/bot.html; nutch-agent@lucene.apache.org)' => 'Nutch/0.8-dev at bilgi.com',
				'HouxouCrawler/Nutch-0.8 (houxou.com\'s nutch-based crawler which serves special interest on-line communities; http://www.houxou.com/crawler; crawler at houxou dot com)' => 'Nutch/0.8 at houxou.com',
				'NutchCVS/0.7.1 (Nutch; http://lucene.apache.org/nutch/bot.html; raphael@unterreuth.de)' => 'NutchCVS/0.7.1 at unterreuth.de',
				'heeii/Nutch-0.9-dev (heeii.com; www.heeii.com; nutch at heeii.com)' => 'heeii/Nutch-0.9-dev at heeii.com',
				'HouxouCrawler/Nutch-0.8.2-dev (houxou.com\'s nutch-based crawler which serves special interest on-line communities; http://www.houxou.com/crawler; crawler at houxou dot com)' => 'Nutch/0.8.2-dev at houxou.com',
				'NutchCVS/Nutch-0.9 (Nutch; http://lucene.apache.org/nutch/bot.html; nutch-agent@lucene.apache.org)' => 'NutchCSV/0.9',
				'HouxouCrawler/Nutch-0.9 (houxou.com\'s nutch-based crawler which serves special interest on-line communities; http://www.houxou.com/crawler; crawler at houxou dot com)' => 'Nutch/0.9 at houxou.com',
				'Zscho.de Crawler/Nutch-1.0-Zscho.de-semantic_patch (Zscho.de Crawler, collecting for machine learning; http://zscho.de/)' => 'Nutch/1.0 at zscho.de',
			),
			'nworm' => array(
				'nWormFeedFinder (http://www.nworm.com)' => 'nwormFeedFinder',
			),
			'Nymesis' => array(
				'Nymesis/2.0 (http://nymesis.com)' => 'Nymesis/2.0',
			),
			'oBot' => array(
				'oBot' => 'oBot',
				'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 4.0; obot)' => 'oBot - b',
				'Mozilla/5.0 (compatible; oBot/2.3.1; +http://www-935.ibm.com/services/us/index.wss/detail/iss/a1029077?cntxt=a1027244)' => 'oBot/2.3.1 b',
				'Mozilla/5.0 (compatible; oBot/2.3.1; +http://filterdb.iss.net/crawler/)' => 'oBot/2.3.1',
			),
			'Ocelli' => array(
				'Ocelli/1.4 (http://www.globalspec.com/Ocelli)' => 'Ocelli/1.4',
			),
			'omgilibot' => array(
				'omgilibot/0.4 +http://omgili.com' => 'omgilibot/0.4',
			),
			'OmniExplorer_Bot' => array(
				'OmniExplorer_Bot/4.02 (+http://www.omni-explorer.com) WorldIndexer' => 'OmniExplorer_Bot/4.02',
				'OmniExplorer_Bot/4.06 (+http://www.omni-explorer.com) WorldIndexer' => 'OmniExplorer_Bot/4.06',
				'OmniExplorer_Bot/6.47 (+http://www.omni-explorer.com) WorldIndexer' => 'OmniExplorer_Bot/6.47',
			),
			'OnetSzukaj' => array(
				'Mozilla/5.0 (compatible; OnetSzukaj/5.0; +http://szukaj.onet.pl)' => 'OnetSzukaj/5.0',
				'Mozilla/5.0 (compatible; OnetSzukaj/5.0; +http://szukaj.onet.pl' => 'OnetSzukaj/5.0 b',
			),
			'Online Domain Tools' => array(
				'Mozilla/5.0 (compatible; Online Domain Tools - HTTP Headers Online/1.0; +http://http-headers.online-domain-tools.com)' => 'HTTP Headers Online/1.0',
				'Mozilla/5.0 (compatible; Online Domain Tools - Online Website Link Checker/1.1; +http://website-link-checker.online-domain-tools.com)' => 'Online Website Link Checker/1.1',
				'Mozilla/5.0 (compatible; Online Domain Tools - Online Sitemap Generator/1.1; +http://sitemap-generator.online-domain-tools.com)' => 'Online Sitemap Generator/1.1',
			),
			'OoyyoBot' => array(
				'OoyyoBot (Used and new cars search engine;+http://www.ooyyo.com) ' => 'OoyyoBot',
			),
			'Open Web Analytics Bot' => array(
				'Open Web Analytics Bot 1.5.4' => 'Open Web Analytics Bot 1.5.4',
			),
			'Openbot' => array(
				'Openfind data gatherer, Openbot/3.0+(robot-response@openfind.com.tw;+http://www.openfind.com.tw/robot.html)' => 'Openbot/3.0',
			),
			'OpenCalaisSemanticProxy' => array(
				'OpenCalaisSemanticProxy' => 'OpenCalaisSemanticProxy',
			),
			'OpenindexSpider' => array(
				'Mozilla/5.0 (compatible; OpenindexDeepSpider/Nutch-1.5-dev; +http://openindex.io/spider.html; systemsATopenindexDOTio)' => 'OpenindexDeepSpider',
				'Mozilla/5.0 (compatible; OpenindexDeepSpider/Nutch-1.5-dev; +http://www.openindex.io/en/webmasters/spider.html; systemsATopenindexDOTio)' => 'OpenindexDeepSpider',
				'Mozilla/5.0 (compatible; OpenindexShallowSpider/Nutch-1.5-dev; +http://www.openindex.io/en/webmasters/spider.html; systemsATopenindexDOTio)' => 'OpenindexShalooowSpider',
				'Mozilla/5.0 (compatible; OpenindexDeepSpider/Nutch-1.5-dev; +http://www.openindex.io/en/webmasters/spider.html)' => 'OpenindexDeepSpider',
				'Mozilla/5.0 (compatible; OpenindexShallowSpider/Nutch-1.5-dev; +http://www.openindex.io/en/webmasters/spider.html)' => 'OpenindexShalooowSpider',
				'Mozilla/5.0 (compatible; OpenindexSpider/Nutch-1.5-dev; +http://www.openindex.io/en/webmasters/spider.html)' => 'OpenindexSpider',
				'Mozilla/5.0 (compatible; OpenindexSpider; +http://www.openindex.io/en/webmasters/spider.html)' => 'OpenindexSpider',
			),
			'OpenWebSpider' => array(
				'OpenWebSpider v0.1.2.B (http://www.openwebspider.org/)' => 'OpenWebSpider v0.1.2.B',
				'OpenWebSpider v0.1.4 (http://www.openwebspider.org/)' => 'OpenWebSpider v0.1.4',
			),
			'Orbiter' => array(
				'Orbiter (+http://www.dailyorbit.com/bot.htm)' => 'Orbiter',
			),
			'OrgbyBot' => array(
				'Orgbybot/OrgbyBot v1.2 (Spidering the net for Orgby; http://www.orgby.com/  ; Orgby.com Search Engine)' => 'OrgbyBot/1.2',
				'Orgbybot/OrgbyBot v1.3 (Spider; http://orgby.com/bot/  ; Orgby.com Search Engine)' => 'OrgbyBot/1.3',
			),
			'OsObot' => array(
				'Mozilla/5.0 (compatible; OsO; http://oso.octopodus.com/abot.html)' => 'OsObot',
			),
			'ownCloud Server Crawler' => array(
				'ownCloud Server Crawler' => 'ownCloud Server Crawler',
			),
			'owsBot' => array(
				'owsBot/0.1 (Nutch; www.oneworldstreet.com; nutch-agent@lucene.apache.org)' => 'owsBot/0.1',
				'owsBot/0.2 (owsBot; www.oneworldstreet.com; owsBot)' => 'owsBot/0.2',
			),
			'Page2RSS' => array(
				'Mozilla/5.0 (compatible;  Page2RSS/0.7; +http://page2rss.com/)' => 'Page2RSS/0.7',
			),
			'PageBitesHyperBot' => array(
				'PageBitesHyperBot/600 (http://www.pagebites.com/)' => 'PageBitesHyperBot/600',
			),
			'PagePeeker' => array(
				'PagePeeker.com' => 'PagePeeker',
				'PagePeeker.com (info: http://pagepeeker.com/robots)' => 'PagePeeker',
				'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.21 (KHTML, like Gecko) Chrome/19.0.1042.0 Safari/535.21 PagePeeker/2.1; +http://pagepeeker.com/robots/' => 'PagePeeker/2.1',
			),
			'page_verifier' => array(
				'page_verifier (http://www.securecomputing.com/goto/pv)' => 'page_verifier',
			),
			'Panscient web crawler' => array(
				'panscient.com' => 'Panscient web crawler',
			),
			'PaperLiBot' => array(
				'Mozilla/5.0 (compatible; PaperLiBot/2.1; http://support.paper.li/entries/20023257-what-is-paper-li)' => 'PaperLiBot/2.1',
			),
			'ParchBot' => array(
				'Mozilla/5.0 (compatible;+ParchBot/1.0;++http://www.parchmenthill.com/search.htm)' => 'ParchBot/1.0',
			),
			'parsijoo' => array(
				'Mozilla/5.0 (compatible; parsijoo; +http://www.parsijoo.ir/; ehsan.mousakazemi@gmail.com)' => 'parsijoo',
			),
			'PayPal IPN' => array(
				'PayPal IPN ( https://www.paypal.com/ipn )' => 'PayPal IPN',
			),
			'Peeplo Screenshot Bot' => array(
				'Peeplo Screenshot Bot/0.20 ( abuse at peeplo dot_com )' => 'Peeplo Screenshot Bot/0.20',
			),
			'Peepowbot' => array(
				'Mozilla/5.0 (compatible; Peepowbot/1.0; +http://www.peepow.com/bot.php)' => 'Peepowbot/1.0',
			),
			'peerindex' => array(
				'peerindex/0.1 (http://www.peerindex.com/; crawler AT peerindex DOT com)' => 'peerindex/0.1',
			),
			'Peew' => array(
				'Mozilla/5.0 (compatible; Peew/1.0; http://www.peew.de/crawler/)' => 'Peew/1.0',
			),
			'PercolateCrawler' => array(
				'percbotspider <ops@percolate.com>' => 'percbotspider',
				'PercolateCrawler/3.1.30 (ops@percolate.com)' => 'PercolateCrawler/3.1.30',
				'PercolateCrawler/4 (ops@percolate.com)' => 'PercolateCrawler/4',
			),
			'pingdom.com_bot' => array(
				'Pingdom.com_bot_version_1.4_(http://www.pingdom.com/)' => 'pingdom.com_bot 1.4',
				'Pingdom GIGRIB (http://www.pingdom.com)' => 'Pingdom GIGRIB',
			),
			'Pinterest' => array(
				'Pinterest/0.1 +http://pinterest.com/' => 'Pinterest/0.1',
			),
			'PiplBot' => array(
				'Mozilla/5.0+(compatible;+PiplBot;++http://www.pipl.com/bot/)' => 'PiplBot',
			),
			'Pixray-Seeker' => array(
				'Pixray-Seeker/1.1 (Pixray-Seeker; crawler@pixray.com)' => 'Pixray-Seeker/1.1',
				'Pixray-Seeker/1.1 (Pixray-Seeker; http://www.pixray.com/pixraybot; crawler@pixray.com)' => 'Pixray-Seeker/1.1',
				'Pixray-Seeker/2.0 (Pixray-Seeker; http://www.pixray.com/pixraybot; crawler@pixray.com)' => 'Pixray-Seeker/2.0',
				'Pixray-Seeker/2.0 (http://www.pixray.com/pixraybot; crawler@pixray.com)' => 'Pixray-Seeker/2.0',
			),
			'Plukkie' => array(
				'Mozilla/5.0 (compatible; Plukkie/1.1; http://www.botje.com/plukkie.htm)' => 'Plukkie/1.1',
				'Mozilla/5.0 (compatible; Plukkie/1.2; http://www.botje.com/plukkie.htm)' => 'Plukkie/1.2',
				'Mozilla/5.0 (compatible; Plukkie/1.3; http://www.botje.com/plukkie.htm)' => 'Plukkie/1.3',
				'Mozilla/5.0 (compatible; Plukkie/1.4; http://www.botje.com/plukkie.htm)' => 'Plukkie/1.4',
				'Mozilla/5.0 (compatible; Plukkie/1.5; http://www.botje.com/plukkie.htm)' => 'Plukkie/1.3',
			),
			'pmoz.info ODP link checker' => array(
				'Mozilla/5.0 (compatible; pmoz.info ODP link checker; +http://pmoz.info/doc/botinfo.htm)' => 'pmoz.info ODP link checker',
			),
			'Pogodak.co.yu' => array(
				'Mozilla/5.0 (compatible; Pogodak.co.yu/3.1)' => 'Pogodak.co.yu/3.1',
			),
			'polixea.de' => array(
				'Mozilla/5.0 (compatible; polixea.de-Robot +http://www.polixea.de)' => 'polixea.de',
			),
			'Pompos' => array(
				'Pompos/1.3 http://dir.com/pompos.html' => 'Pompos/1.3',
			),
			'posterus' => array(
				'posterus (seek.se) +http://www.seek.se/studio/index.php?id=47&t=details' => 'posterus',
			),
			'PostPost' => array(
				'PostPost/1.0 (+http://postpo.st/crawlers)' => 'PostPost/1.0',
				'PostPost/1.0 (+http://postpost.com/crawlers)' => 'PostPost/1.0',
			),
			'pr-cy.ru Screenshot Bot' => array(
				'pr-cy.ru Screenshot Bot' => 'pr-cy.ru Screenshot Bot',
			),
			'ProCogBot' => array(
				'Mozilla/5.0 (compatible; ProCogBot/1.0; +http://www.procog.com/spider.html)' => 'ProCogBot/1.0',
			),
			'ProCogSEOBot' => array(
				'Mozilla/5.0 (compatible; ProCogSEOBot/1.0; +http://www.procog.com/ )' => 'ProCogSEOBot/1.0',
			),
			'proximic' => array(
				'Mozilla/5.0 (compatible; proximic; +http://www.proximic.com/info/spider.php)' => 'proximic',
			),
			'psbot' => array(
				'psbot/0.1 (+http://www.picsearch.com/bot.html)' => 'psbot/0.1',
				'psbot-page (+http://www.picsearch.com/bot.html)' => 'psbot-page',
			),
			'Qirina Hurdler' => array(
				'Qirina Hurdler v. 1.05 10.11.01 (+http://www.qirina.com/hurdler.html)' => 'Qirina Hurdler v. 1.05 10.11.01',
			),
			'Qseero' => array(
				'Qseero v1.0.0' => 'Qseero 1.0.0',
				'Mozilla/5.0 (compatible; Qseero; +http://www.q0.com)' => 'Qseero',
			),
			'Qualidator.com Bot' => array(
				'Mozilla/5.0 (compatible; Qualidator.com Bot 1.0;)' => 'Qualidator.com Bot 1.0',
			),
			'Qualidator.com SiteAnalyzer 1.0' => array(
				'Mozilla/5.0 (compatible; Qualidator.com SiteAnalyzer 1.0;)' => 'Qualidator.com SiteAnalyzer 1.0',
			),
			'Quantcastbot' => array(
				'Mozilla/5.0 (compatible; Quantcastbot/1.0; www.quantcast.com)' => 'Quantcastbot/1.0',
			),
			'QuerySeekerSpider' => array(
				'QuerySeekerSpider ( http://queryseeker.com/bot.html )' => 'QuerySeekerSpider',
			),
			'quickobot' => array(
				'quickobot/quickobot-1 (Quicko Labs; http://quicko.co; robot at quicko dot co)' => 'quickobot-1',
			),
			'R6 bot' => array(
				'R6_FeedFetcher(www.radian6.com/crawler)' => 'R6_FeedFetcher',
				'R6_CommentReader(www.radian6.com/crawler)' => 'R6_CommentReader',
			),
			'RADaR-Bot' => array(
				'RADaR-Bot/Nutch-1.3 (http://radar-bot.com/)' => 'RADaR-Bot',
			),
			'RankurBot' => array(
				'RankurBot/Rankur2.1 (http://rankur.com; info at rankur dot com)' => 'RankurBot/2.1',
			),
			'Readability' => array(
				'Readability/6a54d4 - http://readability.com/about/' => 'Readability/6a54d4',
			),
			'RedBot' => array(
				'RedBot/redbot-1.0 (Rediff.com Crawler; redbot at rediff dot com)' => 'RedBot1.0',
			),
			'Robo Crawler' => array(
				'Robo Crawler 6.4.5 (robocrawler@bb.softbank.co.jp)' => 'Robo Crawler 6.4.5',
			),
			'Robots_Tester' => array(
				'Robots_Tester_http_www.searchenginepromotionhelp.com' => 'Robots_Tester',
			),
			'Robozilla' => array(
				'Robozilla/1.0' => 'Robozilla/1.0',
			),
			'rogerbot' => array(
				'rogerbot/1.0 (http://www.seomoz.org/dp/rogerbot, rogerbot-crawler@seomoz.org)' => 'rogerbot/1.0',
				'rogerbot/1.0 (http://www.seomoz.org/dp/rogerbot, rogerbot-crawler+shiny@seomoz.org)' => 'rogerbot/1.0',
				'rogerbot/1.0 (http://www.seomoz.org/dp/rogerbot, rogerbot-wherecat@moz.com)' => 'rogerbot/1.0',
				'Mozilla/5.0 (compatible; rogerBot/1.0; UrlCrawler; http://www.seomoz.org/dp/rogerbot)' => 'rogerbot/1.0',
				'rogerbot/1.0 (http://moz.com/help/pro/what-is-rogerbot-, rogerbot-crawler+shiny@moz.com)' => 'rogerbot/1.0',
				'rogerbot/1.0 (http://moz.com/help/pro/what-is-rogerbot-, rogerbot-wherecat@moz.com)' => 'rogerbot/1.0',
			),
			'Ronzoobot' => array(
				'Ronzoobot/1.3 (http://www.ronzoo.com/about.php)' => 'Ronzoobot/1.3',
				'Ronzoobot/1.2 (http://www.ronzoo.com/about.php)' => 'Ronzoobot/1.2',
				'Ronzoobot/1.5 (http://www.ronzoo.com/about/)' => 'Ronzoobot/1.5',
				'Ronzoobot/1.6 (http://www.ronzoo.com/about/)' => 'Ronzoobot/1.6',
			),
			'RSSMicro.com RSS/Atom Feed Robot' => array(
				'RSSMicro.com RSS/Atom Feed Robot' => 'RSSMicro.com RSS/Atom Feed Robot',
			),
			'Ruky-Roboter' => array(
				'Ruky-Roboter (Version: 1.06, powered by www.ruky.de +http://www.ruky.de/bot.html)' => 'Ruky-Roboter/1.06',
			),
			'RyzeCrawler' => array(
				'RyzeCrawler/1.1.1 ( http://www.domain2day.nl/crawler/)' => 'RyzeCrawler/1.1.1',
				'RyzeCrawler/1.1.1 (+http://www.domain2day.nl/crawler/)' => 'RyzeCrawler/1.1.1',
			),
			'SAI Crawler' => array(
				'http://domino.research.ibm.com/comm/research_projects.nsf/pages/sai-crawler.callingcard.html' => 'SAI Crawler',
			),
			'SanszBot' => array(
				'SanszBot/1.7(http://www.sansz.org/sanszbot, spider@sansz.org) (spider@sansz.org)' => 'SanszBot/1.7',
			),
			'SBIder' => array(
				'SBIder/0.7 (SBIder; http://www.sitesell.com/sbider.html; http://support.sitesell.com/contact-support.html)' => 'SBIder/0.7',
				'SBIder/0.8-dev (SBIder; http://www.sitesell.com/sbider.html; http://support.sitesell.com/contact-support.html)' => 'SBIder/0.8dev',
				'SBIder/SBIder-0.8.2-dev (http://www.sitesell.com/sbider.html)' => 'SBIder-0.8.2-dev',
				'SBIder/Nutch-1.0-dev (http://www.sitesell.com/sbider.html)' => 'SBIder/1.0',
			),
			'SBSearch' => array(
				'Mozilla/5.0 (compatible; SecretSerachEngineLabs.com-SBSearch/0.9; http://www.secretsearchenginelabs.com/secret-web-crawler.php)' => 'SBSearch/0.9',
			),
			'Scarlett' => array(
				'Mozilla/5.0 (compatible; Scarlett/ 1.0; +http://www.ellerdale.com/crawler.html)' => 'Scarlett/ 1.0',
			),
			'SCFCrawler' => array(
				'SCFCrawler/Nutch-1.8 (Image Crawler for StolenCameraFinder.com; http://www.stolencamerafinder.com/; crawler@stolencamerafinder.com)' => 'SCFCrawler',
			),
			'schibstedsokbot' => array(
				'schibstedsokbot (compatible; Mozilla/5.0; MSIE 5.0; FAST FreshCrawler 6; +http://www.schibstedsok.no/bot/)' => 'schibstedsokbot',
			),
			'ScollSpider' => array(
				'Mozilla/3.0 (compatible; ScollSpider; http://www.webwobot.com)' => 'ScollSpider',
			),
			'Scooter' => array(
				'Scooter/3.3' => 'Scooter/3.3',
			),
			'ScoutJet' => array(
				'Mozilla/5.0 (compatible; ScoutJet; +http://www.scoutjet.com/)' => 'ScoutJet old',
				'Mozilla/5.0 (compatible; ScoutJet; http://www.scoutjet.com/)' => 'ScoutJet',
			),
			'Scrapy' => array(
				'Scrapy/0.22.2 (+http://scrapy.org)' => 'Scrapy/0.22.0',
				'Scrapy/0.24.0 (+http://scrapy.org)' => 'Scrapy/0.24.0',
			),
			'ScreenerBot Crawler' => array(
				'ScreenerBot Crawler Beta 2.0 (+http://www.ScreenerBot.com)' => 'ScreenerBot Crawler Beta 2.0',
			),
			'Search Engine World Robots.txt Validator' => array(
				'Search Engine World Robots.txt Validator at http://www.searchengineworld.com/cgi-bin/robotcheck.cgi' => 'Search Engine World Robots.txt Validator',
			),
			'search.KumKie.com' => array(
				'search.KumKie.com' => 'search.KumKie.com',
			),
			'Search17Bot' => array(
				'Mozilla/5.0 (compatible; Search17Bot/1.1; http://www.search17.com/bot.php)' => 'Search17Bot/1.1',
			),
			'SearchmetricsBot' => array(
				'Mozilla/5.0 (compatible; SearchmetricsBot; http://www.searchmetrics.com/en/searchmetrics-bot/)' => 'SearchmetricsBot',
			),
			'SecurityResearchBot' => array(
				'Mozilla/5.0 (compatible; SecurityResearch.bot; +http://besome1.info/securityresearchbot.html)' => 'SecurityResearchBot',
			),
			'seegnifybot' => array(
				'seegnifybot/1.0.0 (http://www.seegnify.com/bot)' => 'seegnifybot/1.0.0',
				'seebot/1.0.0 (http://www.seegnify.com/bot)' => 'seebot/1.0.0',
			),
			'Semager' => array(
				'Mozilla/5.0 (compatible; Semager/1.4; http://www.semager.de/blog/semager-bots/)' => 'Semager/1.4',
				'Mozilla/5.0 (compatible; Semager/1.4c; +http://www.semager.de/blog/semager-bots/)' => 'Semager/1.4c',
			),
			'Semantifire' => array(
				'Semantifire1/0.20 ( http://www.setooz.com/oozbot.html ; agentname at setooz dot_com )' => 'Semantifire1/0.20',
			),
			'SemrushBot' => array(
				'SemrushBot/0.9' => 'SemrushBot/0.9',
				'SemrushBot/Nutch-1.5-SNAPSHOT' => 'SemrushBot',
				'SemrushBot/0.91' => 'SemrushBot/0.91',
				'SemrushBot/0.92' => 'SemrushBot/0.92',
				'Mozilla/5.0 (compatible; SemrushBot/0.95; +http://www.semrush.com/bot.html)' => 'SemrushBot/0.95',
				'Mozilla/5.0 (compatible; SemrushBot/0.96.2; +http://www.semrush.com/bot.html)' => 'SemrushBot/0.96.2',
				'Mozilla/5.0 (compatible; SemrushBot/0.96.3; +http://www.semrush.com/bot.html)' => 'SemrushBot/0.96.3',
				'Mozilla/5.0 (compatible; SemrushBot/0.97; +http://www.semrush.com/bot.html)' => 'SemrushBot/0.97',
				'Mozilla/5.0 (compatible; SemrushBot/0.96.4; +http://www.semrush.com/bot.html)' => 'SemrushBot/0.96.4',
				'Mozilla/5.0 (compatible; SemrushBot/0.97~bl; +http://www.semrush.com/bot.html)' => 'SemrushBot/0.97',
				'Mozilla/5.0 (compatible; SemrushBot-SA/0.97; +http://www.semrush.com/bot.html)' => 'SemrushBot-SA/0.97',
			),
			'Sensis Web Crawler' => array(
				'Sensis Web Crawler (search_comments\\at\\sensis\\dot\\com\\dot\\au)' => 'Sensis Web Crawler - b',
				'Sensis Web Crawler (search_comments\\\\at\\\\sensis\\\\dot\\\\com\\\\dot\\\\au)' => 'Sensis Web Crawler',
			),
			'Seobility' => array(
				'Urlstat (http://bit.ly/1dJuuzs)' => 'Seobility Urlstat',
				'Seobility (SEO-Check; http://bit.ly/1dJuuzs)' => 'Seobility SEO-Check',
			),
			'SeoCheckBot' => array(
				'SeoCheckBot (FischerNetzDesign Seo Checker, info@fischernetzdesign.de)' => 'SeoCheckBot',
				'SeoCheck (FischerNetzDesign Seo Checker, info@fischernetzdesign.de)' => 'SeoCheck',
				'SeoCheckBot (Seo-Check, http://www.kfsw.de/bot.html)' => 'SeoCheckBot',
			),
			'SEODat' => array(
				'Mozilla/5.0 (compatible; SEODat/0.1 http://crawler.seodat.com)' => 'SEODat/0.1',
			),
			'SEOENGBot' => array(
				'SEOENGBot/1.2 (+http://learn.seoeng.com/seoengbot.htm)' => 'SEOENGBot/1.2 old',
				'SEOENGBot/1.2 (+http://learn.seoengine.com/seoengbot.htm)' => 'SEOENGBot/1.2',
				'SEOENGWorldBot/1.0 (+http://www.seoengine.com/seoengbot.htm)' => 'SEOENGBot/1.0',
				'SEOENGBot/1.2 (+http://www.seoengine.com/seoengbot.htm)' => 'SEOENGBot/1.2 new',
			),
			'SEOkicks-Robot' => array(
				'Mozilla/5.0 (compatible; SEOkicks-Robot +http://www.seokicks.de/robot.html)' => 'SEOkicks-Robot',
				'Mozilla/5.0 (compatible; SEOkicks-Robot; +http://www.seokicks.de/robot.html)' => 'SEOkicks-Robot',
			),
			'Setoozbot' => array(
				'OOZBOT/0.20 ( Setooz vÃ½raznÃ½ ako say-th-uuz, znamenÃ¡ mosty.  ; http://www.setooz.com/oozbot.html ; agentname at setooz dot_com )' => 'OOZBOT/0.20 b',
				'OOZBOT/0.20 ( -- ; http://www.setooz.com/oozbot.html ; agentname at setooz dot_com )' => 'OOZBOT/0.20',
				'Mozilla/5.0 ( compatible; SETOOZBOT/0.30 ; http://www.setooz.com/bot.html ; agentname at setooz dot_com )' => 'SETOOZBOT/0.30 b',
				'Mozilla/5.0 ( compatible; SETOOZBOT/0.30 ; http://www.setooz.com/bot.html )' => 'SETOOZBOT/0.30 a',
				'Setooz/Nutch-1.0 (http://www.setooz.com)' => 'Setoozbot/1.0',
				'SETOOZBOT/5.0 ( compatible; SETOOZBOT/0.30 ; http://www.setooz.com/bot.html )' => 'SETOOZBOT/0.30',
				'SETOOZBOT/5.0 ( http://www.setooz.com/bot.html )' => 'SETOOZBOT/5.0',
			),
			'Setoozbot ' => array(
				'OOZBOT/0.20 ( http://www.setooz.com/oozbot.html ; agentname at setooz dot_com )' => 'OOZBOT/0.20 c',
			),
			'SeznamBot' => array(
				'Mozilla/5.0 (compatible; Seznam screenshot-generator 2.0; +http://fulltext.sblog.cz/screenshot/)' => 'Seznam screenshot-generator 2.0',
				'SeznamBot/2.0 (+http://fulltext.sblog.cz/robot/)' => 'SeznamBot/2.0',
				'SeznamBot/2.0 (+http://fulltext.seznam.cz/)' => 'SeznamBot/2.0',
				'SeznamBot/2.0-Test (+http://fulltext.sblog.cz/robot/)' => 'SeznamBot/2.0-test',
				'SeznamBot/3.0-alpha (+http://fulltext.sblog.cz/)' => 'SeznamBot/3.0-alpha',
				'SeznamBot/3.0-beta (+http://fulltext.sblog.cz/)' => 'SeznamBot/3.0-beta',
				'SeznamBot/3.0-beta (+http://fulltext.sblog.cz/), I' => 'SeznamBot/3.0-beta',
				'SeznamBot/3.0 (+http://fulltext.sblog.cz/)' => 'SeznamBot/3.0',
				'SeznamBot/3.0-test (+http://fulltext.sblog.cz/)' => 'SeznamBot/3.0-test',
				'SeznamBot/3.0-test (+http://fulltext.sblog.cz/), I' => 'SeznamBot/3.0-test',
				'SeznamBot/3.0 (HaF+http://fulltext.sblog.cz/)' => 'SeznamBot/3.0',
				'Mozilla/5.0 (compatible; SeznamBot/3.1-test1; +http://fulltext.sblog.cz/)' => 'SeznamBot/3.1-test',
				'Mozilla/5.0 (compatible; Seznam screenshot-generator 2.1; +http://fulltext.sblog.cz/screenshot/)' => 'Seznam screenshot-generator 2.1',
				'Mozilla/5.0 (Linux; U; Android 4.1.2; cs-cz; Seznam screenshot-generator Build/Q3) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30' => 'Seznam screenshot-generator Q3',
				'Mozilla/5.0 (compatible; SeznamBot/3.2; +http://fulltext.sblog.cz/)' => 'SeznamBot/3.2',
				'Mozilla/5.0 (compatible; SeznamBot/3.2-test1; +http://fulltext.sblog.cz/)' => 'SeznamBot/3.2-test1',
				'SklikBot/2.0 (sklik@firma.seznam.cz;+http://napoveda.sklik.cz/)' => 'SklikBot/2.0',
			),
			'Shareaholicbot' => array(
				'Mozilla/5.0 (compatible; Shareaholicbot/1.0; +http://www.shareaholic.com/bot)' => 'Shareaholicbot/1.0',
			),
			'Shelob' => array(
				'Shelob (shelob@gmx.net)' => 'Shelob',
			),
			'Shim-Crawler' => array(
				'Shim-Crawler(Mozilla-compatible; http://www.logos.ic.i.u-tokyo.ac.jp/crawler/; crawl@logos.ic.i.u-tokyo.ac.jp)' => 'Shim-Crawler',
				'Shim-Crawler(Mozilla-compatible; http://www.logos.ic.i.u-tokyo.ac.jp/crawl/; crawl@logos.ic.i.u-tokyo.ac.jp)' => 'Shim-Crawler - b',
			),
			'ShopWiki' => array(
				'ShopWiki/1.0 ( +http://www.shopwiki.com/wiki/Help:Bot)' => 'ShopWiki/1.0',
			),
			'ShowyouBot' => array(
				'ShowyouBot (http://showyou.com/crawler)' => 'ShowyouBot',
			),
			'silk' => array(
				'Silk/1.0' => 'silk/1.0 -a',
				'silk/1.0 (+http://www.slider.com/silk.htm)/3.7' => 'silk/1.0',
			),
			'Sirketce/Busiverse' => array(
				'Sirketcebot/v.01 (http://www.sirketce.com/bot.html)' => 'Sirketcebot/v.01',
				'Busiversebot/v1.0 (http://www.busiverse.com/bot.php)' => 'Busiversebot/v1.0',
			),
			'sistrix' => array(
				'Mozilla/5.0 (compatible; SISTRIX Crawler; http://crawler.sistrix.net/)' => 'sistrix',
			),
			'SiteCondor' => array(
				'Mozilla/5.0 (compatible; SiteCondor; http://www.sitecondor.com)' => 'SiteCondor',
			),
			'Sitedomain-Bot' => array(
				'Sitedomain-Bot(Sitedomain-Bot 1.0, http://www.sitedomain.de/sitedomain-bot/)' => 'Sitedomain-Bot 1.0',
			),
			'SkreemRBot' => array(
				'Mozilla/5.0 (compatible; SkreemRBot +http://skreemr.com)' => 'SkreemRBot',
			),
			'smart.apnoti.com Robot' => array(
				'smart.apnoti.com Robot/v1.34 (http://smart.apnoti.com/en/aboutApnotiWebCrawler.html)' => 'smart.apnoti.com Robot/v1.34',
			),
			'snap.com' => array(
				'snap.com beta crawler v0' => 'snap.com beta crawler v0',
			),
			'Snapbot' => array(
				'Snapbot/1.0' => 'Snapbot/1.0',
				'Snapbot/1.0 (+http://www.snap.com)' => 'Snapbot/1.0 b',
				'Snapbot/1.0 (Snap Shots, +http://www.snap.com)' => 'Snapbot/1.0 c',
				'Snapbot/1.0 (Site Search Crawler, +http://www.snap.com)' => 'Snapbot/1.0 d',
			),
			'SnapBot' => array(
				'Mozilla/5.0 (compatible; SnapPreviewBot; en-US; rv:1.8.0.9) Gecko/20061206 Firefox/1.5.0.9' => 'SnapPreviewBot',
			),
			'Snappy' => array(
				'Snappy/1.1 ( http://www.urltrends.com/ )' => 'Snappy/1.1',
			),
			'SniffRSS' => array(
				'SniffRSS/0.5beta (+http://www.blogator.com/)' => 'SniffRSS/0.5beta',
			),
			'socialbm_bot' => array(
				'Mozilla/5.0 (compatible; socialbm_bot/1.0; +http://spider.socialbm.net)' => 'socialbm_bot/1.0',
				'socialbm_bot http://spider.socialbm.net' => 'socialbm_bot',
			),
			'sogou spider' => array(
				'Sogou web spider/4.0(+http://www.sogou.com/docs/help/webmasters.htm#07)' => 'Sogou web spider/4.0',
				'Sogou develop spider/4.0(+http://www.sogou.com/docs/help/webmasters.htm#07)' => 'Sogou develop spider/4.0',
				'sogou spider' => 'sogou spider',
				'sogou web spider http://www.sogou.com/docs/help/webmasters.htm#07' => 'sogou spider',
				'sogou web spider(+http://www.sogou.com/docs/help/webmasters.htm#07)' => 'sogou spider',
				'Sogou web spider/3.0(+http://www.sogou.com/docs/help/webmasters.htm#07)' => 'Sogou web spider/3.0',
				'Sogou-Test-Spider/4.0 (compatible; MSIE 5.5; Windows 98)' => 'Sogou-Test-Spider/4.0',
				'Sogou Web Spider' => 'Sogou web spider',
				'Sogou web spider/4.0' => 'Sogou web spider/4.0',
				'Sogou web spider/4.0l-2m!' => 'Sogou web spider/4.0l-2m!',
			),
			'SolomonoBot' => array(
				'SolomonoBot/1.04 (http://www.solomono.ru)' => 'SolomonoBot/1.04',
			),
			'Sosospider' => array(
				'Sosospider+(+http://help.soso.com/webspider.htm)' => 'Sosospider',
				'Mozilla/5.0(compatible; Sosospider/2.0; +http://help.soso.com/webspider.htm)' => 'Sosospider/2.0',
				'Mozilla/5.0(compatible;Sosospider/2.0;+http://help.soso.com/webspider.htm)' => 'Sosospider/2.0',
				'sosoimagespider+(+http://help.soso.com/soso-image-spider.htm)' => 'Sosoimagespider',
			),
			'spbot' => array(
				'Mozilla/5.0 (compatible; spbot/2.0.3; +http://www.seoprofiler.com/bot/ )' => 'spbot/2.0.3',
				'Mozilla/5.0 (compatible; spbot/2.0.4; +http://www.seoprofiler.com/bot )' => 'spbot/2.0.4',
				'Mozilla/5.0 (compatible; spbot/1.0; +http://www.seoprofiler.com/bot/ )' => 'spbot/1.0',
				'Mozilla/5.0 (compatible; spbot/2.1; +http://www.seoprofiler.com/bot )' => 'spbot/2.1',
				'Mozilla/5.0 (compatible; spbot/3.0; +http://www.seoprofiler.com/bot )' => 'spbot/3.0',
				'Mozilla/5.0 (compatible; spbot/1.1; +http://www.seoprofiler.com/bot/ )' => 'spbot/1.1',
				'Mozilla/5.0 (compatible; spbot/2.0; +http://www.seoprofiler.com/bot/ )' => 'spbot/2.0',
				'Mozilla/5.0 (compatible; spbot/1.2; +http://www.seoprofiler.com/bot/ )' => 'spbot/1.2',
				'Mozilla/5.0 (compatible; spbot/2.0.1; +http://www.seoprofiler.com/bot/ )' => 'spbot/2.0.1',
				'Mozilla/5.0 (compatible; spbot/2.0.2; +http://www.seoprofiler.com/bot/ )' => 'spbot/2.0.2',
				'Mozilla/5.0 (compatible; spbot/3.1; +http://www.seoprofiler.com/bot )' => 'spbot/3.1',
				'Mozilla/5.0 (compatible; spbot/4.0a; +http://www.seoprofiler.com/bot )' => 'spbot/4.0a',
				'Mozilla/5.0 (compatible; spbot/4.0b; +http://www.seoprofiler.com/bot )' => 'spbot/4.0b',
				'Mozilla/5.0 (compatible; spbot/4.0.1; +http://www.seoprofiler.com/bot )' => 'spbot/4.0.1',
				'Mozilla/5.0 (compatible; spbot/4.0; +http://www.seoprofiler.com/bot )' => 'spbot/4.0',
				'Mozilla/5.0 (compatible; spbot/4.0.3; +http://www.seoprofiler.com/bot )' => 'spbot/4.0.3',
				'Mozilla/5.0 (compatible; spbot/4.0.2; +http://www.seoprofiler.com/bot )' => 'spbot/4.0.2',
				'Mozilla/5.0 (compatible; spbot/4.0.4; +http://www.seoprofiler.com/bot )' => 'spbot/4.0.4',
				'Mozilla/5.0 (compatible; spbot/4.0.5; +http://www.seoprofiler.com/bot )' => 'spbot/4.0.5',
				'Mozilla/5.0 (compatible; spbot/4.0.6; +http://www.seoprofiler.com/bot )' => 'spbot/4.0.6',
				'Mozilla/5.0 (compatible; spbot/4.0.7; +http://OpenLinkProfiler.org/bot )' => 'spbot/4.0.7',
				'Mozilla/5.0 (compatible; spbot/4.0.9; +http://OpenLinkProfiler.org/bot )' => 'spbot/4.0.9',
				'Mozilla/5.0 (compatible; spbot/4.0.8; +http://OpenLinkProfiler.org/bot )' => 'spbot/4.0.8',
				'Mozilla/5.0 (compatible; spbot/4.1.0; +http://OpenLinkProfiler.org/bot )' => 'spbot/4.1.0',
			),
			'Speedy' => array(
				'Speedy Spider (http://www.entireweb.com/about/search_tech/speedy_spider/)' => 'Speedy Spider',
				'Speedy Spider (Entireweb; Beta/1.2; http://www.entireweb.com/about/search_tech/speedyspider/)' => 'Speedy Spider Beta/1.2',
				'Mozilla/5.0 (compatible; Speedy Spider; http://www.entireweb.com/about/search_tech/speedy_spider/)' => 'Speedy Spider',
				'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) Speedy Spider (http://www.entireweb.com/about/search_tech/speedy_spider/)' => 'Speedy Spider',
				'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) Speedy Spider for SpeedyAds (http://www.entireweb.com/about/search_tech/speedy_spider/)' => 'Speedy Spider',
				'Speedy Spider (Submit your site at http://www.entireweb.com/free_submission/)' => 'Speedy Spider',
			),
			'SpiderLing' => array(
				'Mozilla/5.0 (compatible; SpiderLing (a SPIDER for LINGustic research); http://nlp.fi.muni.cz/projects/biwec/)' => 'SpiderLing',
				'Mozilla/5.0 (compatible; SpiderLing (a SPIDER for LINGustic research); +http://nlp.fi.muni.cz/projects/biwec/)' => 'SpiderLing',
			),
			'Spinn3r' => array(
				'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2.1; aggregator:Spinn3r (Spinn3r 3.1); http://spinn3r.com/robot) Gecko/20021130' => 'Spinn3r 3.1',
				'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.0.19; aggregator:Spinn3r (Spinn3r 3.1); http://spinn3r.com/robot) Gecko/2010040121 Firefox/3.0.19' => 'Spinn3r 3.1',
			),
			'Spock Crawler' => array(
				'Spock Crawler (http://www.spock.com/crawler)' => 'Spock Crawler',
			),
			'SpokeSpider' => array(
				'SpokeSpider/1.0 (http://support.spoke.com/webspider/) Mozilla/5.0 (not really)' => 'SpokeSpider/1.0',
			),
			'sproose' => array(
				'sproose/0.1-alpha (sproose crawler; http://www.sproose.com/bot.html; crawler@sproose.com)' => 'sproose/0.1-alpha',
				'sproose/0.1 (sproose bot; http://www.sproose.com/bot.html; crawler@sproose.com)' => 'sproose/0.1',
			),
			'Sproose' => array(
				'sproose/1.0beta (sproose bot; http://www.sproose.com/bot.html; crawler@sproose.com)' => 'Sproose/1.0beta',
			),
			'SputnikBot' => array(
				'Mozilla/5.0 (compatible; SputnikBot/2.3; +http://corp.sputnik.ru/webmaster)' => 'SputnikBot/2.3',
			),
			'SrevBot' => array(
				'SrevBot/2.0 (SrevBot; http://winsrev.com/bot.html; bot@winsrev.com)' => 'SrevBot/2.0',
				'SrevBot/1.2 (SrevBot; http://winsrev.com/bot.html; bot@winsrev.comg)' => 'SrevBot/1.2',
			),
			'SSL-Crawler' => array(
				'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.101 Safari/537.36; SSL-Crawler: http://crawler.dcsec.uni-hannover.de' => 'SSL-Crawler',
			),
			'SSLBot' => array(
				'Mozilla/5.0 (compatible; SSLBot/1.0;  http://www.sslstats.com/sslbot)' => 'SSLBot/1.0',
			),
			'StackRambler' => array(
				'StackRambler/2.0 (MSIE incompatible)' => 'StackRambler/2.0',
			),
			'StatoolsBot' => array(
				'StatoolsBot (+http://www.statools.com/bot.html)' => 'StatoolsBot',
			),
			'Steeler' => array(
				'Steeler/3.2 (http://www.tkl.iis.u-tokyo.ac.jp/~crawler/)' => 'Steeler/3.2',
				'Steeler/3.3 (http://www.tkl.iis.u-tokyo.ac.jp/~crawler/)' => 'Steeler/3.3',
				'Mozilla/5.0 (compatible; Steeler/3.5; http://www.tkl.iis.u-tokyo.ac.jp/~crawler/)' => 'Steeler/3.5',
			),
			'STINGbot' => array(
				'Mozilla/5.0 (compatible; STINGbot/1.0; +http://136.186.231.16)' => 'STINGbot/1.0',
			),
			'stq_bot' => array(
				'stq_bot (+http://www.searchteq.de)' => 'stq_bot',
			),
			'Strokebot' => array(
				'Stroke.cz (http://stroke.cz)' => 'Strokebot',
			),
			'suggybot' => array(
				'Mozilla/5.0 (compatible; suggybot v0.01a, http://blog.suggy.com/was-ist-suggy/suggy-webcrawler/)' => 'suggybot/0.01a',
			),
			'SurcentroBot' => array(
				'SurcentroBot' => 'SurcentroBot',
			),
			'Surphace Scout' => array(
				'Surphace Scout&v4.0 - scout at surphace dot com' => 'Surphace Scout/4.0',
			),
			'SurveyBot' => array(
				'SurveyBot/2.3 (Whois Source)' => 'SurveyBot/2.3',
			),
			'SWEBot' => array(
				'Mozilla/5.0 (compatible; SWEBot/1.0; +http://swebot.net)' => 'SWEBot/1.0',
				'Mozilla/5.0 (compatible; SWEBot/1.0; +http://swebot-crawler.net)' => 'SWEBot/1.0',
			),
			'SygolBot' => array(
				'SygolBot http://www.sygol.com' => 'SygolBot',
			),
			'Symfony Spider' => array(
				'Symfony Spider (http://symfony.com/spider)' => 'Symfony Spider',
			),
			'SynooBot' => array(
				'SynooBot/0.7.1 (SynooBot; http://www.synoo.de/bot.html; webmaster@synoo.com)' => 'SynooBot/0.7.1',
				'SynooBot (compatible; Synoobot/0.7.1; http://www.synoo.com/search/bot.html)' => 'SynooBot/0.7.1 com',
			),
			'Szukacz' => array(
				'Szukacz/1.5 (robot; www.szukacz.pl/html/jak_dziala_robot.html; info@szukacz.pl)' => 'Szukacz/1.5',
				'Szukacz/1.5 (robot; www.szukacz.pl/jakdzialarobot.html; info@szukacz.pl)' => 'Szukacz/1.5 b',
			),
			'Szukankobot' => array(
				'Szukankobot /1.0 (+http://www.szukanko.pl/addurl.php)' => 'Szukankobot /1.0',
			),
			'Tagoobot' => array(
				'Mozilla/5.0 (compatible; Tagoobot/3.0; +http://www.tagoo.ru)' => 'Tagoobot/3.0',
			),
			'taptubot' => array(
				'taptubot *** please read http://www.taptu.com/corp/taptubot ***' => 'taptubot',
			),
			'Technoratibot' => array(
				'Technoratibot/7.0' => 'Technoratibot/7.0',
				'Technoratibot/8.0' => 'Technoratibot/8.0',
			),
			'TeragramCrawler' => array(
				'TeragramCrawler' => 'TeragramCrawler',
			),
			'textractor' => array(
				'textractor.queuekeeper/0.1 (+http://ufal.mff.cuni.cz/project/textractor/, textractor@ufal.mff.cuni.cz)' => 'textractor.queuekeeper/0.1',
				'textractor.harvester/h7/1.0 (+http://ufal.mff.cuni.cz/project/textractor/, textractor@ufal.mff.cuni.cz)' => 'textractor.harvester/h7/1.0',
				'textractor.harvester/h3/1.0 (+http://ufal.mff.cuni.cz/project/textractor/, textractor@ufal.mff.cuni.cz)' => 'textractor.harvester/h3/1.0',
				'textractor.harvester/h2/1.0 (+http://ufal.mff.cuni.cz/project/textractor/, textractor@ufal.mff.cuni.cz)' => 'textractor.harvester/h2/1.0',
				'textractor.harvester/h27/1.0 (+http://ufal.mff.cuni.cz/project/textractor/, textractor@ufal.mff.cuni.cz)' => 'textractor.harvester/h27/1.0',
				'textractor.harvester/h24/1.0 (+http://ufal.mff.cuni.cz/project/textractor/, textractor@ufal.mff.cuni.cz)' => 'textractor.harvester/h24/1.0',
				'textractor.harvester/h5/1.0 (+http://ufal.mff.cuni.cz/project/textractor/, textractor@ufal.mff.cuni.cz)' => 'textractor.harvester/h5/1.0',
				'textractor.harvester/h39/1.0 (+http://ufal.mff.cuni.cz/project/textractor/, textractor@ufal.mff.cuni.cz)' => 'textractor.harvester/h39/1.0',
				'textractor.harvester/h37/1.0 (+http://ufal.mff.cuni.cz/project/textractor/, textractor@ufal.mff.cuni.cz)' => 'textractor.harvester/h37/1.0',
				'textractor.harvester/h38/1.0 (+http://ufal.mff.cuni.cz/project/textractor/, textractor@ufal.mff.cuni.cz)' => 'textractor.harvester/h38/1.0',
				'textractor.harvester/h12/1.0 (+http://ufal.mff.cuni.cz/project/textractor/, textractor@ufal.mff.cuni.cz)' => 'textractor.harvester/h12/1.0',
				'textractor.harvester/h34/1.0 (+http://ufal.mff.cuni.cz/project/textractor/, textractor@ufal.mff.cuni.cz)' => 'textractor.harvester/h34/1.0',
			),
			'Theophrastus' => array(
				'Mozilla/5.0 (compatible; Theophrastus/2.0; +http://users.cs.cf.ac.uk/N.A.Smith/theophrastus.php)' => 'Theophrastus/2.0',
			),
			'Thumbnail.CZ robot' => array(
				'Thumbnail.CZ robot 1.1 (http://thumbnail.cz/why-no-robots-txt.html)' => 'Thumbnail.CZ robot 1.1',
			),
			'ThumbShots-Bot' => array(
				'ThumbShots-Bot (+http://thumbshots.in/bot.html)' => 'ThumbShots-Bot',
			),
			'thumbshots-de-Bot' => array(
				'thumbshots-de-Bot (Version: 1.02, powered by www.thumbshots.de)' => 'thumbshots-de-Bot 1.02',
				'thumbshots-de-bot (+http://www.thumbshots.de/)' => 'thumbshots-de-bot',
			),
			'Thumbshots.ru' => array(
				'Mozilla/5.0 (compatible; Thumbshots.ru; +http://thumbshots.ru/bot) Firefox/3' => 'Thumbshots.ru',
			),
			'ThumbSniper' => array(
				'ThumbSniper (http://thumbsniper.com)' => 'ThumbSniper',
			),
			'TinEye' => array(
				'TinEye/1.0; +http://www.tineye.com/' => 'TinEye/1.0',
				'TinEye/1.1 (http://tineye.com/crawler.html)' => 'TinEye/1.1',
				'TinEye-bot/0.02 (see http://www.tineye.com/crawler.html)' => 'TinEye-bot/0.02',
			),
			'TomTom places company search' => array(
				'Mozilla/5.0 (compatible; heritrix/3.1.1 +http://places.tomtom.com/crawlerinfo)' => 'TomTom places company search',
			),
			'Topicbot' => array(
				'Mozilla/5.0 (compatible; Topicbot/12.0rc-2; +http://topicbot.awardspace.us/)' => 'Topicbot/12.0rc-2',
			),
			'Toread-Crawler' => array(
				'Mozilla/4.0 (Toread-Crawler/1.1; +http://news.toread.cc/crawler.php)' => 'Toread-Crawler/1.1',
			),
			'Touche' => array(
				'Touche (+http://www.touche.com.ve)' => 'Touche',
			),
			'trendictionbot' => array(
				'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-GB; rv:1.0; trendictionbot0.4.2; trendiction media ssppiiddeerr; http://www.trendiction.com/bot/; please let us know of any problems; ssppiiddeerr at trendiction.com) Gecko/20071127 Firefox/2.0.0.11' => 'trendictionbot0.4.2',
			),
			'trendictionbot ' => array(
				'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-GB; rv:1.0; trendictionbot0.5.0; trendiction search; http://www.trendiction.de/bot; please let us know of any problems; web at trendiction.com) Gecko/20071127 Firefox/3.0.0.11' => 'trendictionbot0.5.0',
			),
			'TurnitinBot' => array(
				'TurnitinBot/2.0 (http://www.turnitin.com/robot/crawlerinfo.html)' => 'TurnitinBot/2.0',
				'TurnitinBot/2.1 (http://www.turnitin.com/robot/crawlerinfo.html)' => 'TurnitinBot/2.1',
				'TurnitinBot/3.0 (http://www.turnitin.com/robot/crawlerinfo.html)' => 'TurnitinBot/3.0',
			),
			'TutorGigBot' => array(
				'TutorGigBot/1.5 ( +http://www.tutorgig.info )' => 'TutorGigBot',
			),
			'TwengaBot' => array(
				'TwengaBot/1.1 (+http://www.twenga.com/bot.html)' => 'TwengaBot/1.1',
				'TwengaBot-Discover (http://www.twenga.fr/bot-discover.html)' => 'TwengaBot-Discover',
				'TwengaBot' => 'TwengaBot',
			),
			'Twiceler' => array(
				'Mozilla/5.0 (Twiceler-0.9 http://www.cuil.com/twiceler/robot.html)' => 'Twiceler-0.9',
				'Twiceler-0.9 http://www.cuill.com/twiceler/robot.html' => 'Twiceler-0.9 b',
			),
			'Twikle' => array(
				'Twikle/1.0 , http://twikle.com , contact@twikle.com' => 'Twikle/1.0',
			),
			'Twingly Recon' => array(
				'Mozilla/5.0 (compatible; Twingly Recon; twingly.com)' => 'Twingly Recon',
			),
			'UASlinkChecker' => array(
				'Mozilla/5.0 (compatible; UASlinkChecker/1.0; +http://user-agent-string.info/UASlinkChecker)' => 'UASlinkChecker/1.0',
			),
			'uMBot' => array(
				'Mozilla/5.0 (compatible; uMBot-FC/1.0; mailto: crawling@ubermetrics-technologies.com)' => 'uMBot-FC/1.0',
				'Mozilla/5.0 (compatible; uMBot-LN/1.0; mailto: crawling@ubermetrics-technologies.com)' => 'uMBot-LN/1.0',
			),
			'UnisterBot' => array(
				'Mozilla/5.0 (compatible; UnisterBot; crawler@unister.de)' => 'UnisterBot',
			),
			'UnwindFetchor' => array(
				'UnwindFetchor/1.0 (+http://www.gnip.com/)' => 'UnwindFetchor/1.0',
			),
			'updated' => array(
				'updated/0.1-beta (updated; http://www.updated.com; crawler@updated.com)' => 'updated/0.1-beta',
				'updated/0.1-alpha (updated crawler; http://www.updated.com; crawler@updated.com)' => 'updated/0.1-alpha',
			),
			'Updownerbot' => array(
				'Updownerbot (+http://www.updowner.com/bot)' => 'Updownerbot',
			),
			'UptimeDog' => array(
				'UptimeDog Robot (www.uptimedog.com)' => 'UptimeDog',
			),
			'UptimeRobot' => array(
				'Mozilla/5.0 (compatible; UptimeRobot/1.0; http://www.uptimerobot.com/)' => 'UptimeRobot/1.0',
			),
			'URLAppendBot' => array(
				'Mozilla/5.0 (compatible; URLAppendBot/1.0; +http://www.profound.net/urlappendbot.html)' => 'URLAppendBot/1.0',
			),
			'urlfan-bot' => array(
				'urlfan-bot/1.0; +http://www.urlfan.com/site/bot/350.html' => 'urlfan-bot/1.0',
			),
			'Urlfilebot (Urlbot)' => array(
				'Mozilla/5.0 (compatible; Urlfilebot/2.2; +http://urlfile.com/bot.html)' => 'Urlfilebot/2.2',
			),
			'Vagabondo' => array(
				'Vagabondo/3.0 (webagent at wise-guys dot nl)' => 'Vagabondo/3.0',
				'Mozilla/4.0 (compatible;  Vagabondo/4.0Beta; webcrawler at wise-guys dot nl; http://webagent.wise-guys.nl/; http://www.wise-guys.nl/)' => 'Vagabondo/4.0Beta',
				'Mozilla/4.0 (compatible;  Vagabondo/4.0; http://webagent.wise-guys.nl/)' => 'Vagabondo/4.0',
				'Mozilla/4.0 (compatible;  Vagabondo/4.0; webcrawler at wise-guys dot nl; http://webagent.wise-guys.nl/)' => 'Vagabondo/4.0',
				'Mozilla/4.0 (compatible;  Vagabondo/4.0; webcrawler at wise-guys dot nl; http://webagent.wise-guys.nl/; http://www.wise-guys.nl/)' => 'Vagabondo/4.0',
				'Mozilla/4.0 (compatible;  Vagabondo/4.0/EU; http://webagent.wise-guys.nl/)' => 'Vagabondo/4.0/EU',
			),
			'Vedma' => array(
				'Mozilla/5.0 (Compatible; Vedma/0.91Beta; +http://www.vedma.ru/bot.htm)' => 'Vedma/0.91Beta',
			),
			'VERASYS 2k' => array(
				'VERASYS 2k Mozilla/4.0 (compatible; en) (compatible; MSIE 6.0; Windows NT 5.2; (+ http://web.verasys.ro); SV1; Unix; .NET CLR 1.1.4322)' => 'VERASYS 2k',
			),
			'Vermut' => array(
				'Mozilla/5.0 (compatible; Vermut +http://vermut.aol.com)' => 'Vermut',
			),
			'Vespa Crawler' => array(
				'Vespa Crawler' => 'Vespa Crawler',
			),
			'VideoSurf_bot' => array(
				'Mozilla/5.0 (compatible; VideoSurf_bot +http://www.videosurf.com/bot.html)' => 'VideoSurf_bot',
			),
			'virus_detector' => array(
				'virus_detector (virus_harvester@securecomputing.com)' => 'virus_detector',
			),
			'Visbot' => array(
				'Visbot/1.1 (Visvo.com - The Category Search Engine!; http://www.visvo.com/bot.html; bot@visvo.com)' => 'Visbot/1.1',
				'VisBot/2.0 (Visvo.com Crawler; http://www.visvo.com/bot.html; bot@visvo.com)' => 'Visbot/2.0',
				'Visbot/2.0 (+http://www.visvo.com/en/webmasters.jsp;bot@visvo.com)' => 'Visbot/2.0',
			),
			'VMBot' => array(
				'VMBot/0.7.2 (VMBot; http://www.VerticalMatch.com/; vmbot@tradedot.com)' => 'VMBot/0.7.2',
				'VMBot/0.9 (VMBot; http://www.verticalmatch.com; vmbot@tradedot.com)' => 'VMBot/0.9',
			),
			'void-bot' => array(
				'void-bot/0.1 (bot@void.be; http://www.void.be/)' => 'void-bot/0.1',
			),
			'VoilaBot' => array(
				'Mozilla/4.0 (compatible; MSIE 5.0; Windows 95) VoilaBot BETA 1.2 (http://www.voila.com/)' => 'VoilaBot BETA 1.2',
				'Mozilla/5.0 (Windows; U; Windows NT 5.1; fr; rv:1.8.1) VoilaBot BETA 1.2 (support.voilabot@orange-ftgroup.com)' => 'VoilaBot BETA 1.2',
				'Mozilla/5.0 (Windows; U; Windows NT 5.1;fr;rv:1.8.1) VoilaBotCollector BETA 0.1  (http://www.voila.com/)' => 'VoilaBotCollector BETA 0.1',
				'Mozilla/5.0 (Windows NT 5.1; U; Win64; fr; rv:1.8.1) VoilaBot BETA 1.2 (support.voilabot@orange-ftgroup.com)' => 'VoilaBot BETA 1.2',
			),
			'voltron' => array(
				'voltron' => 'voltron',
			),
			'VORTEX' => array(
				'VORTEX/1.2 (+http://marty.anstey.ca/robots/vortex/)' => 'VORTEX/1.2',
			),
			'voyager' => array(
				'voyager/2.0 (http://www.kosmix.com/crawler.html)' => 'voyager/2.0',
				'voyager/1.0 (+http://www.kosmix.com/html/crawler.html)' => 'voyager/1.0',
			),
			'VWBot' => array(
				'VWBOT/Nutch-0.9-dev (VWBOT Nutch Crawler; http://vwbot.cs.uiuc.edu; vwbot@cs.uiuc.edu)' => 'VWBot/Nutch-0.9-dev',
			),
			'WASALive-Bot' => array(
				'Mozilla/5.0 (compatible; WASALive-Bot ; http://blog.wasalive.com/wasalive-bots/)' => ' WASALive-Bot',
			),
			'WatchMouse' => array(
				'WatchMouse/18990 (http://watchmouse.com/ ; hk)' => 'WatchMouse/18990 hk',
				'WatchMouse/18990 (http://watchmouse.com/ ; ny)' => 'WatchMouse/18990 ny',
				'WatchMouse/18990 (http://watchmouse.com/ ; se.watchmouse.com)' => 'WatchMouse/18990 se.watchmouse.com',
				'WatchMouse/18990 (http://watchmouse.com/ ; it)' => 'WatchMouse/18990 it',
				'WatchMouse/18990 (http://watchmouse.com/ ; bc.watchmouse.com)' => 'WatchMouse/18990 bc',
				'WatchMouse/18990 (http://watchmouse.com/ ; uk)' => 'WatchMouse/18990 uk',
				'WatchMouse/18990 (http://watchmouse.com/ ; d2.watchmouse.com)' => 'WatchMouse/18990 d2.watchmouse.com',
				'WatchMouse/18990 (http://watchmouse.com/ ; liz)' => 'WatchMouse/18990 liz',
				'WatchMouse/18990 (http://watchmouse.com/ ; d3.watchmouse.com)' => 'WatchMouse/18990 d3.watchmouse.com',
				'WatchMouse/18990 (http://watchmouse.com/ ; gab)' => 'WatchMouse/18990 gab',
			),
			'WBSearchBot' => array(
				'Mozilla/5.0 (compatible; WBSearchBot/1.1; +http://www.warebay.com/bot.html)' => 'WBSearchBot/1.1',
			),
			'Web-Monitoring' => array(
				'Mozilla/5.0 (compatible; Web-Monitoring/1.0; +http://monoid.nic.ru/)' => 'Web-Monitoring/1.0',
			),
			'Web-sniffer' => array(
				'Web-sniffer/1.0.31 (+http://web-sniffer.net/)' => 'Web-sniffer/1.0.31',
			),
			'WebAlta Crawler' => array(
				'WebAlta Crawler/1.3.33 (http://www.webalta.net/ru/about_webmaster.html) (Windows; U; Windows NT 5.1; ru-RU)' => 'WebAlta Crawler/1.3.33',
				'WebAlta Crawler/1.3.34 (http://www.webalta.net/ru/about_webmaster.html) (Windows; U; Windows NT 5.1; ru-RU)' => 'WebAlta Crawler/1.3.34',
				'WebAlta Crawler/1.3.25 (http://www.webalta.net/ru/about_webmaster.html) (Windows; U; Windows NT 5.1; ru-RU)' => 'WebAlta Crawler/1.3.25',
			),
			'WebarooBot' => array(
				'WebMiner (Web Miner; http://64.124.122.252/feedback.html)' => 'WebMiner (Web Miner)',
				'RufusBot (Rufus Web Miner; http://64.124.122.252/feedback.html)' => 'RufusBot (Rufus Web Miner)',
				'WebarooBot (Webaroo Bot; http://64.124.122.252/feedback.html)' => 'WebarooBot (Webaroo Bot)',
				'WebarooBot (Webaroo Bot; http://www.webaroo.com/rooSiteOwners.html)' => 'WebarooBot (Webaroo Bot) b',
			),
			'WebCookies' => array(
				'WebCookies/1.0 (+http://webcookies.info/faq/#agent)' => 'WebCookies/1.0',
			),
			'WebCorp' => array(
				'Mozilla/5.0 (compatible; WebCorp/5.0; +http://www.webcorp.org.uk)' => 'WebCorp/5.0',
			),
			'WebImages' => array(
				'WebImages 0.3 ( http://herbert.groot.jebbink.nl/?app=WebImages )' => 'WebImages 0.3',
			),
			'webinatorbot' => array(
				'webinatorbot 1.0; +http://www.webinator.de' => 'webinatorbot 1.0',
				'webinatorbot 1.1; +http://www.webinator.de' => 'webinatorbot 1.1',
			),
			'webmastercoffee' => array(
				'mozilla/5.0 (compatible; webmastercoffee/0.7; +http://webmastercoffee.com/about)' => 'webmastercoffee/0.7',
			),
			'WebNL' => array(
				'Mozilla/5.0 (compatible; WebNL; +http://www.web.nl/webmasters/spider.html; helpdesk@web.nl)' => 'WebNL',
			),
			'WebRankSpider' => array(
				'WebRankSpider/1.37 (+http://ulm191.server4you.de/crawler/)' => 'WebRankSpider/1.37',
			),
			'Webscope Crawler' => array(
				'Webscope/Nutch-0.9-dev (http://www.cs.washington.edu/homes/mjc/agent.html)' => 'Webscope Crawler',
			),
			'WebThumbnail' => array(
				'Mozilla/5.0 (compatible; WebThumbnail/3.x; Website Thumbnail Generator; +http://webthumbnail.org)' => 'WebThumbnail/3.x',
				'Mozilla/5.0 (compatible; WebThumbnail/2.2; Website Thumbnail Generator; +http://webthumbnail.org)' => 'WebThumbnail/2.2',
			),
			'WebWatch/Robot_txtChecker' => array(
				'WebWatch/Robot_txtChecker' => 'WebWatch/Robot_txtChecker',
			),
			'wectar' => array(
				'wectar/Nutch-0.9 (wectar - wectar extracted from the glorious web; http://goosebumps4all.net/wectar)' => 'wectar/Nutch-0.9',
				'wectar/Nutch-0.9 (nectar extracted form the glorious web; http://goosebumps4all.net/wectar; see website)' => 'wectar/Nutch-0.9 b',
			),
			'WeSEE' => array(
				'WeSEE:Search/0.1 (Alpha, http://www.wesee.com/en/support/bot/)' => 'WeSEE:Search/0.1 (Alpha)',
				'WeSEE:Search' => 'WeSEE:Search',
				'WeSEE:Ads/PageBot (http://www.wesee.com/bot/)' => 'WeSEE:Ads/PageBot',
				'WeSEE' => 'WeSEE',
				'WeSEE:Ads/PictureBot (http://www.wesee.com/bot/)' => 'WeSEE:Ads/PictureBot',
			),
			'Whoismindbot' => array(
				'Whoismindbot/1.0 (+http://www.whoismind.com/bot.html)' => 'Whoismindbot/1.0',
			),
			'WikioFeedBot' => array(
				'WikioFeedBot 1.0 (http://www.wikio.com)' => 'WikioFeedBot 1.0',
			),
			'wikiwix-bot' => array(
				'wikiwix-bot-3.0' => 'wikiwix-bot/3.0',
			),
			'Willow Internet Crawler' => array(
				'Willow Internet Crawler by Twotrees V2.1' => 'Willow Internet Crawler 2.1',
			),
			'WillyBot' => array(
				'WillyBot/1.1 (http://www.willyfogg.com/info/willybot)' => 'WillyBot/1.1',
			),
			'WinkBot' => array(
				'WinkBot/0.06 (Wink.com search engine web crawler; http://www.wink.com/Wink:WinkBot; winkbot@wink.com)' => 'WinkBot/0.06',
			),
			'WinWebBot' => array(
				'WinWebBot/1.0; (Balaena Ltd, UK); http://www.balaena.com/winwebbot.html; winwebbot@balaena.com;)' => 'WinWebBot/1.0',
			),
			'WIRE' => array(
				'WIRE/0.10 (Linux; i686; Bot,Robot,Spider,Crawler)' => 'WIRE/0.10',
				'WIRE/0.11 (Linux; i686; Bot,Robot,Spider,Crawler,aromano@cli.di.unipi.it)' => 'WIRE/0.11',
				'WIRE/0.11 (Linux; i686; Robot,Spider,Crawler,aromano@cli.di.unipi.it)' => 'WIRE/0.11 b',
			),
			'WMCAI_robot' => array(
				'WMCAI-robot (http://www.topicmaster.jp/wmcai/crawler.html)' => 'WMCAI_robot',
			),
			'Woko' => array(
				'Woko 3.0' => 'Woko 3.0',
				'Woko robot 3.0' => 'Woko robot 3.0',
			),
			'woriobot' => array(
				'Mozilla/5.0 (compatible; woriobot +http://worio.com)' => 'woriobot',
				'Mozilla/5.0 (compatible; woriobot support [at] zite [dot] com +http://zite.com)' => 'woriobot',
			),
			'Wotbox' => array(
				'Wotbox/2.0 (bot@wotbox.com; http://www.wotbox.com)' => 'Wotbox/2.0',
				'Wotbox/2.01 (+http://www.wotbox.com/bot/)' => 'Wotbox/2.01',
			),
			'wsAnalyzer' => array(
				'wsAnalyzer/1.0; ++http://www.wsanalyzer.com/bot.html' => 'wsAnalyzer/1.0',
			),
			'wscheck.com' => array(
				'wscheck.com/1.0.0 (+http://wscheck.com/)' => 'wscheck.com/1.0.0',
			),
			'www.fi crawler' => array(
				'www.fi crawler, contact crawler@www.fi' => 'www.fi crawler',
			),
			'wwwster' => array(
				'wwwster/1.4 (Beta, mailto:gue@cis.uni-muenchen.de)' => 'wwwster/1.4 Beta',
			),
			'x28-job-bot' => array(
				'x28-job-bot; +http://x28.ch/bot.html' => 'x28-job-bot',
			),
			'XmarksFetch' => array(
				'Mozilla/5.0 (compatible; XmarksFetch/1.0; +http://www.xmarks.com/about/crawler; info@xmarks.com)' => 'XmarksFetch/1.0',
			),
			'XML Sitemaps Generator' => array(
				'XML Sitemaps Generator 1.0' => 'XML Sitemaps Generator 1.0',
				'Mozilla/5.0 (compatible; XML Sitemaps Generator; http://www.xml-sitemaps.com) Gecko XML-Sitemaps/1.0' => 'XML Sitemaps Generator/1.0',
			),
			'XoviBot' => array(
				'XoviBot/1.0' => 'XoviBot/1.0',
				'Mozilla/5.0 (compatible; XoviBot/2.0; +http://www.xovibot.net/)' => 'XoviBot/2.0',
			),
			'XRL' => array(
				'XRL/3.00 (Linux; i686; en-us) (+http://metamark.net/about)' => 'XRL/3.00',
			),
			'Yaanb' => array(
				'Yaanb/1.5.001 (compatible; Win64;)' => 'Yaanb/1.5.001',
				'Yaanb/1.5.001 (compatible; Win64;+http://www.yaanb.com/company/bot.hmtl)' => 'Yaanb/1.5.001 b',
			),
			'yacybot' => array(
				'yacybot (i386 Linux 2.6.28-gentoo-r5; java 1.5.0_18; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (i386 Linux 2.6.28-11-generic; java 1.6.0_13; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (amd64 Linux 2.6.24-23-generic; java 1.6.0_07; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (x86 Windows Vista 6.1; java 1.6.0_13; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (i386 Linux 2.6.28-13-generic; java 1.6.0_13; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (amd64 Windows 7 6.1; java 1.6.0_14; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (i386 Linux 2.6.23; java 1.6.0_06; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (amd64 Linux 2.6.18-164.el5; java 1.6.0; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (i386 Linux 2.6.24-23-generic; java 1.6.0_16; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (i386 Linux 2.6.23; java 1.6.0_17; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (amd64 Linux 2.6.26-2-openvz-amd64; java 1.6.0_12; UTC/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (amd64 Linux 2.6.32-gentoo; java 1.6.0_17; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (x86 Windows 2003 5.2; java 1.6.0_16; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (i386 Linux 2.6.26-2-686; java 1.6.0_0; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (i386 Linux 2.6.30-2-686; java 1.6.0_0; SystemV/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (i386 Linux 2.6.31-18-generic; java 1.6.0_0; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (amd64 Windows 7 6.1; java 1.6.0_21; Europe/fr) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (amd64 Linux 2.6.31-22-server; java 1.6.0_18; Asia/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (i386 Linux 2.6.31-21-generic; java 1.6.0_0; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (amd64 Linux 2.6.28-18-generic; java 1.6.0_16; GMT/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (x86 Windows XP 5.1; java 1.6.0_18; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (amd64 Linux 2.6.26-2-amd64; java 1.6.0_0; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (i386 Linux 2.6.32-22-generic; java 1.6.0_20; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (x86 Windows 2003 5.2; java 1.6.0_20; America/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; i386 Linux 3.12-1-686-pae; java 1.7.0_21; Europe/fr) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (amd64 Linux 2.6.26-2-amd64; java 1.6.0_20; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (x86 Windows XP 5.1; java 1.6.0_21; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (amd64 Linux 2.6.18-164.15.1.el5xen; java 1.6.0_0; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (amd64 Windows 7 6.1; java 1.6.0_18; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 2.6.32-34-server; java 1.6.0_26; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows 7 6.1; java 1.6.0_29; Europe/fr) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.0.0-14-generic; java 1.6.0_23; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; i386 Linux 2.6.37.6-0.5-desktop; java 1.6.0_20; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (webportal/global; amd64 Linux 2.6.32-5-amd64; java 1.6.0_18; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 2.6.32-5-amd64; java 1.6.0_18; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.0.0-15-server; java 1.6.0_23; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.2.1-gentoo-r2; java 1.6.0_22; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.0.0-12-generic; java 1.6.0_26; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.2.1-gentoo-r2; java 1.6.0_24; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (webportal-global; amd64 Linux 2.6.32-5-amd64; java 1.6.0_18; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 2.6.32-5-amd64; java 1.6.0_18; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 2.6.26-2-amd64; java 1.6.0_18; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows Server 2008 6.0; java 1.7.0_03; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows 7 6.1; java 1.6.0_24; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 2.6.32-custom; java 1.6.0_26; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.1.10-hardened; java 1.7.0_03-icedtea; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; x86_64 Mac OS X 10.6.8; java 1.6.0_29; Asia/ru) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; i386 Linux 2.6.32-39-generic-pae; java 1.6.0_20; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; i386 Linux 3.0.0-17-generic-pae; java 1.6.0_23; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 2.6.32-5-amd64; java 1.6.0_26; Atlantic/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.0.0-17-generic; java 1.6.0_23; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.2.13-1-ARCH; java 1.7.0_03-icedtea; Europe/fr) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.0.0-17-generic; java 1.6.0_23; America/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 2.6.32-5-xen-amd64; java 1.6.0_18; Europe/fr) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; i386 Linux 3.0.0-17-generic; java 1.6.0_23; America/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; x86 Windows 7 6.1; java 1.6.0_31; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.2.0-23-generic; java 1.6.0_24; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows 7 6.1; java 1.6.0_31; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 2.6.32-40-server; java 1.6.0_20; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.1.10-1-desktop; java 1.6.0_22; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 2.6.38-14-generic; java 1.6.0_22; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.2.0-21-generic; java 1.7.0_03-icedtea; America/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.2.0-2-amd64; java 1.6.0_24; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 2.6.32-40-generic; java 1.6.0_20; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows Server 2008 R2 6.1; java 1.6.0_31; America/pt) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 2.6.38-8-generic; java 1.6.0_22; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.1.10-1.9-default; java 1.6.0_24; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 2.6.32-5-amd64; java 1.6.0_18; US/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows Server 2008 R2 6.1; java 1.6.0_29; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows 7 6.1; java 1.6.0_31; America/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.3.4-1-ARCH; java 1.6.0_24; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 2.6.32-41-server; java 1.6.0_26; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; i386 Linux 3.2.0-23-generic-pae; java 1.7.0_03; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; x86 Windows 2003 5.2; java 1.6.0_32; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.2.0-24-generic; java 1.6.0_24; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows 7 6.1; java 1.6.0_25; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.2.0-32-generic; java 1.6.0_24; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; i386 Linux 2.6.32-5-686; java 1.6.0_18; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows 7 6.1; java 1.6.0_23; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.2.0-3-amd64; java 1.6.0_24; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.5.0-27-generic; java 1.7.0_03; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; i386 Linux 3.4.2-linode44; java 1.6.0_27; America/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.8.0-21-generic; java 1.6.0_27; Pacific/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld-global; amd64 Linux 3.2.0-4-amd64; java 1.6.0_24; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.2.0-4-amd64; java 1.6.0_27; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld-global; amd64 Linux 3.2.0-35-generic; java 1.7.0_09; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows XP 5.2; java 1.7.0_04; America/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (webportal-global; amd64 Windows 7 6.1; java 1.7.0_04; America/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 2.6.32-49-server; java 1.6.0_27; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.8.0-23-generic; java 1.6.0_27; Pacific/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.8.13-gentoo; java 1.7.0_21; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows 7 6.1; java 1.7.0_09; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows 7 6.1; java 1.7.0_04; Asia/ja) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.8.0-19-generic; java 1.7.0_25; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows Server 2008 R2 6.1; java 1.7.0_25; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.2.0-4-amd64; java 1.7.0_03; Etc/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.5.0-27-generic; java 1.7.0_25; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows NT (unknown) 6.2; java 1.7.0_05; Africa/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (webportal-global; x86 Windows Vista 6.0; java 1.7.0_25; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows 7 6.1; java 1.7.0_25; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows 8 6.2; java 1.7.0_25; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.10.15-1-MANJARO; java 1.7.0_40; Asia/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.2.0-4-amd64; java 1.7.0_25; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld-global; amd64 Windows 7 6.1; java 1.7.0_02-ea; America/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; i386 Linux 3.2.0-23-generic; java 1.6.0_27; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.10.17-gentoo; java 1.7.0_45; UTC/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows 7 6.1; java 1.7.0_45; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows Server 2012 6.2; java 1.7.0_25; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows Server 2012 6.2; java 1.7.0_51; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows Server 2008 6.0; java 1.7.0_25; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.12.15-gentoo; java 1.7.0_55; Europe/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (webportal/global; x86_64 Mac OS X 10.9.2; java 1.6.0_65; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (/global; amd64 Linux 3.14-0.bpo.1-amd64; java 1.7.0_55; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Linux 3.2.0-4-amd64; java 1.7.0_55; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; x86 Windows XP 5.1; java 1.7.0_55; Asia/en) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; amd64 Windows 7 6.1; java 1.7.0_60; Europe/de) http://yacy.net/bot.html' => 'yacybot',
				'yacybot (freeworld/global; x86 Windows 7 6.1; java 1.7.0_25; Europe/de) http://yacy.net/bot.html' => 'yacybot',
			),
			'Yahoo!' => array(
				'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)' => 'Yahoo! Slurp',
				'Mozilla/5.0 (compatible; Yahoo! Slurp China; http://misc.yahoo.com.cn/help.html)' => 'Yahoo! Slurp China',
				'Mozilla/5.0 (compatible; Yahoo! Slurp/3.0; http://help.yahoo.com/help/us/ysearch/slurp)' => 'Yahoo! Slurp/3.0',
				'Y!J-BRI/0.0.1 crawler ( http://help.yahoo.co.jp/help/jp/search/indexing/indexing-15.html )' => 'Y!J-BRI/0.0.1',
				'Y!J-BSC/1.0 (http://help.yahoo.co.jp/help/jp/blog-search/)' => 'Y!J-BSC/1.0',
				'Mozilla/5.0 (Yahoo-MMCrawler/4.0; mailto:vertical-crawl-support@yahoo-inc.com)' => 'Yahoo-MMCrawler/4.0',
				'Yahoo! Site Explorer Feed Validator http://help.yahoo.com/l/us/yahoo/search/siteexplorer/manage/' => 'Yahoo! Site Explorer Feed Validator',
				'Y!J-BRO/YFSJ crawler (compatible; Mozilla 4.0; MSIE 5.5; http://help.yahoo.co.jp/help/jp/search/indexing/indexing-15.html; YahooFeedSeekerJp/2.0)' => 'Y!J-BRO/YFSJ',
				'Y!J-BRW/1.0 crawler (http://help.yahoo.co.jp/help/jp/search/indexing/indexing-15.html)' => 'Y!J-BRW/1.0',
				'Y!J-BRJ/YATS crawler (http://listing.yahoo.co.jp/support/faq/int/other/other_001.html)' => 'Y!J-BRJ/YATS',
				'Y!J-BSC/1.0 crawler (http://help.yahoo.co.jp/help/jp/blog-search/)' => 'Y!J-BSC/1.0',
				'Y!J-BRJ/YATS crawler (http://help.yahoo.co.jp/help/jp/search/indexing/indexing-15.html)' => 'Y!J-BRJ/YATS',
				'YahooCacheSystem' => 'YahooCacheSystem',
			),
			'YandexBot' => array(
				'Yandex/1.01.001 (compatible; Win16; I)' => 'Yandex/1.01.001',
				'Yandex/1.01.001 (compatible; Win16; P)' => 'Yandex/1.01.001',
				'YandexSomething/1.0' => 'YandexSomething/1.0',
				'Yandex/1.01.001 (compatible; Win16; H)' => 'Yandex/1.01.001',
				'Yandex/1.01.001 (compatible; Win16; m)' => 'Yandex/1.01.001',
				'Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)' => 'YandexBot/3.0',
				'Mozilla/5.0 (compatible; YandexImages/3.0; +http://yandex.com/bots)' => 'YandexImages/3.0',
				'Mozilla/5.0 (compatible; YandexBot/3.0; MirrorDetector; +http://yandex.com/bots)' => 'YandexBot/3.0-MirrorDetector',
				'Mozilla/5.0 (compatible; YandexWebmaster/2.0; +http://yandex.com/bots)' => 'YandexWebmaster/2.0',
				'Mozilla/5.0 (compatible; YandexMedia/3.0; +http://yandex.com/bots)' => 'YandexMedia/3.0',
				'Mozilla/5.0 (compatible; YandexNews/3.0; +http://yandex.com/bots)' => 'YandexNews/3.0',
				'Mozilla/5.0 (compatible; YandexMetrika/2.0; +http://yandex.com/bots)' => 'YandexMetrika/2.0',
				'Mozilla/5.0 (compatible; YandexCatalog/3.0; +http://yandex.com/bots)' => 'YandexCatalog/3.0',
				'Mozilla/5.0 (compatible; YandexDirect/3.0; +http://yandex.com/bots)' => 'YandexDirect/3.0',
				'Mozilla/5.0 (compatible; YandexImageResizer/2.0; +http://yandex.com/bots)' => 'YandexImageResizer/2.0',
				'Yandex.Server/2009.5' => 'Yandex.Server/2009.5',
				'Yandex.Server/2010.9' => 'Yandex.Server/2010.9',
				'Mozilla/5.0 (compatible; YandexFavicons/1.0; +http://yandex.com/bots)' => 'YandexFavicons/1.0',
				'Mozilla/5.0 (compatible; YandexAntivirus/2.0; +http://yandex.com/bots)' => 'YandexAntivirus/2.0',
				'Mozilla/5.0 (compatible; YandexVideo/3.0; +http://yandex.com/bots)' => 'YandexVideo/3.0',
				'Mozilla/5.0 (compatible; YandexBlogs/0.99; robot; +http://yandex.com/bots)' => 'YandexBlogs/0.99',
				'Mozilla/5.0 (compatible; YandexZakladki/3.0; +http://yandex.com/bots)' => 'YandexZakladki/3.0',
			),
			'Yanga' => array(
				'Yanga WorldSearch Bot v1.1/beta (http://www.yanga.co.uk/)' => 'Yanga v1.1/beta',
			),
			'YioopBot' => array(
				'Mozilla/5.0 (compatible; YioopBot; +http://www.yioop.com/bot.php)' => 'YioopBot',
				'Mozilla/5.0 (compatible; gofind; +http://govid.mobi/bot.php)' => 'gofind',
				'Mozilla/5.0 (compatible; YioopBot; +http://173.13.143.74/bot.php)' => 'YioopBot',
			),
			'YodaoBot' => array(
				'YodaoBot/1.0 (http://www.yodao.com/help/webmaster/spider/; )' => 'YodaoBot/1.0',
				'Mozilla/5.0 (compatible; YodaoBot/1.0; http://www.yodao.com/help/webmaster/spider/; )' => 'YodaoBot/1.0',
				'Mozilla/5.0 (compatible;YodaoBot-Image/1.0;http://www.youdao.com/help/webmaster/spider/;)' => 'YodaoBot-Image/1.0',
			),
			'Yoono Bot' => array(
				'Mozilla/5.0 (compatible; Yoono; http://www.yoono.com/)' => 'Yoono Bot',
			),
			'YoudaoBot' => array(
				'Mozilla/5.0 (compatible; YoudaoBot/1.0; http://www.youdao.com/help/webmaster/spider/; )' => 'YoudaoBot/1.0',
			),
			'YowedoBot' => array(
				'YowedoBot/Yowedo 1.0 (Search Engine crawler for yowedo.com; http://yowedo.com/en/partners.html; crawler@yowedo.com)' => 'YowedoBot/1.0',
			),
			'YRSpider' => array(
				'yrspider (Mozilla/5.0 (compatible; YRSpider; +http://www.yunrang.com/yrspider.html))' => 'YRSpider',
			),
			'YYSpider' => array(
				'Mozilla/5.0 (compatible; YYSpider; +http://www.yunyun.com/spider.html)' => 'YYSpider',
			),
			'ZACATEK_CZ' => array(
				'ZACATEK_CZ_BOT (www.zacatek.cz)' => 'ZACATEK_CZ_BOT',
			),
			'ZeerchBot' => array(
				'Mozilla/5.0 (compatible; ZB-1; +http://www.zeerch.com/bot.php)' => 'ZeerchBot ZB-1',
				'Mozilla/5.0 (compatible; LA1; +http://www.zeerch.com/bot.php)' => 'ZeerchBot LA1',
				'Mozilla/5.0 (compatible; LA2; +http://www.zeerch.com/zeerch2/bot.php)' => 'ZeerchBot LA2',
			),
			'Zeusbot' => array(
				'Zeusbot/0.07 (Ulysseek\'s web-crawling robot; http://www.zeusbot.com; agent@zeusbot.com)' => 'Zeusbot/0.07',
			),
			'ZookaBot' => array(
				'Zookabot/2.1;++http://zookabot.com' => 'ZookaBot/2.1',
			),
			'Zookabot' => array(
				'Zookabot/2.0;++http://zookabot.com' => 'Zookabot/2.0',
				'Zookabot/2.2;++http://zookabot.com' => 'Zookabot/2.2',
				'Zookabot/2.4;++http://zookabot.com' => 'Zookabot/2.4',
				'Zookabot/2.5;++http://zookabot.com' => 'Zookabot/2.5',
			),
			'ZoomSpider (ZSEBOT)' => array(
				'ZoomSpider - wrensoft.com [ZSEBOT]' => 'ZoomSpider (ZSEBOT)',
			),
			'ZumBot' => array(
				'ZumBot/1.0 (ZUM Search; http://help.zum.com/inquiry)' => 'ZumBot/1.0',
				'Mozilla/5.0 (compatible; ZumBot/1.0; http://help.zum.com/inquiry)' => 'ZumBot/1.0',
			),
			'ZyBorg' => array(
				'Mozilla/4.0 compatible ZyBorg/1.0 Dead Link Checker (wn.dlc@looksmart.net; http://www.WISEnutbot.com)' => 'ZyBorg/1.0 Dead Link Checker',
				'Mozilla/4.0 compatible ZyBorg/1.0 (wn-14.zyborg@looksmart.net; http://www.WISEnutbot.com)' => 'ZyBorg/1.0',
				'Mozilla/4.0 compatible ZyBorg/1.0 (wn-16.zyborg@looksmart.net; http://www.WISEnutbot.com)' => 'ZyBorg/1.0 - b',
			),
			'^Nail' => array(
				'^Nail (http://CaretNail.com)' => '^Nail',
			),
		);

		/**
		 * Agent utilisateur à détecter
		 * 
		 * @var string
		 */
		protected $agent;

		/**
		 * Type d'agent utilisateur
		 * 
		 * @var string
		 */
		protected $type;

		/**
		 * Nom du périphérique
		 * 
		 * @var string
		 */
		protected $device;

		/**
		 * Nom de la plate-forme
		 * 
		 * @var string
		 */
		protected $platform;

		/**
		 * Famille de la plate-forme
		 * 
		 * @var string
		 */
		protected $platformFamily;

		/**
		 * Nom du navigateur
		 * 
		 * @var string
		 */
		protected $browser;

		/**
		 * Version du navigateur
		 * 
		 * @var string
		 */
		protected $browserVersion;

		/**
		 * Nom du robot
		 * 
		 * @var string
		 */
		protected $robot;

		/**
		 * Famille du robot
		 * 
		 * @var string
		 */
		protected $robotFamily;


		/**
		 * Constructeur du détecteur
		 *
		 * @param string $agent Agent utilisateur à détecter [si non renseigné alors sera récupéré de la variable globale « $_SERVER »]
		 */
		public function __construct($agent = NULL) {

			// Si l'agent n'est pas renseigné on tente de le récupérer depuis la variable du serveur
			if(NULL === $agent) {
				if(isset($_SERVER['HTTP_USER_AGENT'])) {
					$agent = $_SERVER['HTTP_USER_AGENT'];
				} else {
					$agent = '';
				}
			}
			$this->setAgent($agent);
		}

		/**
		 * Retourne l'agent utilisateur
		 * 
		 * @return string L'agent utilisateur
		 */
		public function getAgent() {
			return $this->agent;
		}

		/**
		 * Modifie l'agent utilisateur
		 * 
		 * @param string $agent Le nouvel agent utilisateur
		 */
		public function setAgent($agent) {
			$this->agent = $agent;

			// Réinitialisation des variables
			$this->type = NULL;
			$this->device = NULL;
			$this->platform = NULL;
			$this->platformFamily = NULL;
			$this->browser = NULL;
			$this->browserVersion = NULL;
			$this->robot = NULL;
			$this->robotFamily = NULL;

			// On recherche parmi les crawlers
			foreach(self::$robots as $family => $robots) {
				foreach($robots as $test => $name) {

					// Si l'agent correspond à un crawler, on récupère les informations puis on arrête là
					if($test == $agent) {
						$this->type = 'Robot';
						$this->device = 'Other';
						$this->robot = $name;
						$this->robotFamily = $family;
						return;
					}
				}
			}

			// On recherche parmi les navigateurs
			foreach(self::$browsers as $type => $browsers) {
				foreach($browsers as $regex => $name) {

					// Si l'agent correspond à un navigateur, on récupère les informations et on arrête de rechercher
					if(preg_match($regex, $agent, $version)) {
						$this->type = $type;
						$this->browser = $name;

						// La version peut éventuellement être récupérer depuis l'expression régulière
						if(isset($version[1])) {
							$this->browserVersion = $version[1];
						}

						break 2;
					}
				}
			}

			// La plate-forme du navigateur peut être fixée
			foreach(self::$browserPlatforms as $family => $browsers) {
				foreach($browsers as $name => $platform) {

					// Si l'agent correspond au navigateur on fixe sa plate-forme
					if($name == $this->browser) {
						$this->platform = $platform;
						$this->platformFamily = $family;
						break 2;
					}
				}
			}

			// Si la plate-forme n'est pas encore fixée on tente de la trouver
			if(empty($this->platform)) {
				foreach(self::$platforms as $family => $platforms) {
					foreach($platforms as $regex => $name) {

						// Si l'agent correspond à une plate-forme, on récupère les informations et on arrête de rechercher
						if(preg_match($regex, $agent)) {
							$this->platform = $name;
							$this->platformFamily = $family;
							break 1;
						}
					}
				}
			}

			// On recherche ensuite le périphérique
			foreach(self::$devices as $type => $devices) {
				foreach($devices as $regex) {

					// Si un périphérique correspond, on récupère son nom avant d'arrêter de rechercher
					if(preg_match($regex, $agent)) {
						$this->device = $type;
						break 2;
					}
				}
			}

			// Sinon on fixe le périphérique manuellement selon le type trouvé auparavant
			if(empty($this->device)) {
				if('Other' === $this->type || 'Library' === $this->type || 'Validator' === $this->type || 'Useragent Anonymizer' === $this->type) {
					$this->device = 'Other';
				} else if('Mobile Browser' === $this->type || 'Wap Browser' === $this->type) {
					$this->device = 'Smartphone';
				} else {
					$this->device = 'Personal computer';
				}
			}
		}

		/**
		 * Retourne un tableau d'informations trouvées sur l'agent utilisateur
		 * 
		 * @return array Informations trouvées sur l'agent utilisateur
		 */
		public function getInfos() {
			$array = array();
			$array['agent'] = $this->agent;
			$array['type'] = $this->type ?: 'unknown';
			$array['device'] = $this->device ?: 'unknown';
			if(!empty($this->platform)) {
				$array['platform'] = $this->platform;
				$array['platform-family'] = $this->platformFamily;
			}
			if(!$this->isRobot()) {
				$array['name'] = $this->browser ?: 'unknown';
				$array['version'] = $this->browserVersion ?: 'unknown';
			} else {
				$array['name'] = $this->robot;
				$array['family'] = $this->robotFamily;
			}
			return $array;
		}

		/**
		 * Retourne le type de l'agent utilisateur
		 * 
		 * @return string Le type de l'agent utilisateur
		 */
		public function getType() {
			return $this->type;
		}

		/**
		 * Indique si l'agent utilisateur est d'un navigateur
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un navigateur
		 */
		public function isBrowser() {
			return 'Browser' === $this->type || 'Offline Browser' === $this->type || 'Mobile Browser' === $this->type || 'Wap Browser' === $this->type;
		}

		/**
		 * Indique si l'agent utilisateur est d'un crawler
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un crawler
		 */
		public function isRobot() {
			return 'Robot' === $this->type;
		}

		/**
		 * Indique si l'agent utilisateur est d'un client de messagerie
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un client de messagerie
		 */
		public function isEmailClient() {
			return 'Email client' === $this->type;
		}

		/**
		 * Indique si l'agent utilisateur est d'une librairie
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'une librairie
		 */
		public function isLibrary() {
			return 'Library' === $this->type;
		}

		/**
		 * Indique si l'agent utilisateur est d'un validateur
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un validateur
		 */
		public function isValidator() {
			return 'Validator' === $this->type;
		}

		/**
		 * Indique si l'agent utilisateur est d'un lecteur de flux
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un lecteur de flux
		 */
		public function isFeedReader() {
			return 'Feed Reader' === $this->type;
		}

		/**
		 * Indique si l'agent utilisateur est d'un lecteur multimédia
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un lecteur multimédia
		 */
		public function isMultimediaPlayer() {
			return 'Multimedia Player' === $this->type;
		}

		/**
		 * Indique si l'agent utilisateur est d'un anonymeur
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un anonymeur
		 */
		public function isUseragentAnonymizer() {
			return 'Useragent Anonymizer' === $this->type;
		}

		/**
		 * Retourne le nom du périphérique de l'agent utilisateur
		 * 
		 * @return string Le nom du périphérique de l'agent utilisateur
		 */
		public function getDevice() {
			return $this->device;
		}

		/**
		 * Indique si l'agent utilisateur est d'un ordinateur personnel
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un ordinateur personnel
		 */
		public function isPersonalComputer() {
			return 'Personal computer' === $this->device;
		}

		/**
		 * Indique si l'agent utilisateur est d'un smartphone
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un smartphone
		 */
		public function isSmartphone() {
			return 'Smartphone' === $this->device;
		}

		/**
		 * Indique si l'agent utilisateur est d'une tablette
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'une tablette
		 */
		public function isTablet() {
			return 'Tablet' === $this->device;
		}

		/**
		 * Indique si l'agent utilisateur est d'une console de jeux
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'une console de jeux
		 */
		public function isGameConsole() {
			return 'Game console' === $this->device;
		}

		/**
		 * Indique si l'agent utilisateur est d'une télévision
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'une télévision
		 */
		public function isSmartTV() {
			return 'Smart TV' === $this->device;
		}

		/**
		 * Indique si l'agent utilisateur est d'un assistant personnel
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un assistant personnel
		 */
		public function isPDA() {
			return 'PDA' === $this->device;
		}

		/**
		 * Indique si l'agent utilisateur est d'un ordinateur vestimentaire
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un ordinateur vestimentaire
		 */
		public function isWearableComputer() {
			return 'Wearable computer' === $this->device;
		}

		/**
		 * Retourne le nom de la plate-forme de l'agent utilisateur
		 * 
		 * @return string Le nom de la plate-forme de l'agent utilisateur
		 */
		public function getPlatform() {
			return $this->platform;
		}

		/**
		 * Retourne la famille de la plate-forme de l'agent utilisateur
		 * 
		 * @return string La famille de la plate-forme de l'agent utilisateur
		 */
		public function getPlatformFamily() {
			return $this->platformFamily;
		}

		/**
		 * Indique si l'agent utilisateur est d'une plate-forme sous Windows
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'une plate-forme sous Windows
		 */
		public function isWindows() {
			return 'Windows' === $this->platformFamily;
		}

		/**
		 * Indique si l'agent utilisateur est d'une plate-forme sous Linux
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'une plate-forme sous Linux
		 */
		public function isLinux() {
			return 'Linux' === $this->platformFamily;
		}

		/**
		 * Indique si l'agent utilisateur est d'une plate-forme sous Mac
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'une plate-forme sous Mac
		 */
		public function isMac() {
			return 'Mac OS' === $this->platformFamily || 'OS X' === $this->platformFamily;
		}

		/**
		 * Indique si l'agent utilisateur est d'une plate-forme sous Android
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'une plate-forme sous Android
		 */
		public function isAndroid() {
			return 'Android' === $this->platformFamily;
		}

		/**
		 * Indique si l'agent utilisateur est d'une plate-forme sous iOS
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'une plate-forme sous iOS
		 */
		public function isIOS() {
			return 'iOS' === $this->platformFamily;
		}

		/**
		 * Retourne le nom du navigateur de l'agent utilisateur
		 * 
		 * @return string Le nom du navigateur de l'agent utilisateur
		 */
		public function getBrowser() {
			return $this->browser;
		}

		/**
		 * Retourne la version du navigateur de l'agent utilisateur
		 * 
		 * @return string La version du navigateur de l'agent utilisateur
		 */
		public function getBrowserVersion() {
			return $this->browserVersion;
		}

		/**
		 * Indique si l'agent utilisateur est d'un navigateur Google Chrome
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un navigateur Google Chrome
		 */
		public function isChrome() {
			return 'Chrome' === $this->browser;
		}

		/**
		 * Indique si l'agent utilisateur est d'un navigateur Mozilla Firefox
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un navigateur Mozilla Firefox
		 */
		public function isFirefox() {
			return 'Firefox' === $this->browser;
		}

		/**
		 * Indique si l'agent utilisateur est d'un navigateur Internet Explorer
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un navigateur Internet Explorer
		 */
		public function isIE() {
			return 'IE' === $this->browser;
		}

		/**
		 * Indique si l'agent utilisateur est d'un navigateur Safari
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un navigateur Safari
		 */
		public function isSafari() {
			return 'Safari' === $this->browser;
		}

		/**
		 * Indique si l'agent utilisateur est d'un navigateur Opera
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un navigateur Opera
		 */
		public function isOpera() {
			return 'Opera' === $this->browser;
		}

		/**
		 * Retourne le nom du crawler de l'agent utilisateur
		 * 
		 * @return string Le nom du crawler de l'agent utilisateur
		 */
		public function getRobot() {
			return $this->robot;
		}

		/**
		 * Retourne la famille du crawler de l'agent utilisateur
		 * 
		 * @return string La famille du crawler de l'agent utilisateur
		 */
		public function getRobotFamily() {
			return $this->robotFamily;
		}

		/**
		 * Indique si l'agent utilisateur est d'un crawler Googlebot
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un crawler Googlebot
		 */
		public function isGooglebot() {
			return 'Googlebot' === $this->robotFamily;
		}

		/**
		 * Indique si l'agent utilisateur est d'un crawler MSN / Bing
		 * 
		 * @return boolean Vrai si l'agent utilisateur est d'un crawler MSN / Bing
		 */
		public function isMSNBot() {
			return 'MSNBot' === $this->robotFamily || 'bingbot' === $this->robotFamily;
		}
	}
?>