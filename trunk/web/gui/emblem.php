<?php
namespace gui
{
	class emblem extends gui_base
	{
		private $guild_id = 0;

		public function build()
		{
			global $dbh;
			$im = null;
			$ebm = null;


			if ( !function_exists('gd_info') )
			{
				$img = file_get_contents('images'.DIRECTORY_SEPARATOR.'no_ebm.png');
				header($this->header);
				echo $img;
				exit;
			}



			try
			{
				$query = 'SELECT `emblem_len`, `emblem_data` FROM `guild` WHERE `guild_id` = :guild_id';

				$sth = $dbh->prepare($query);
				$sth->bindValue(':guild_id', $this->guild_id, \PDO::PARAM_INT);

				if ( $sth->execute() )
				{
					$data = $sth->fetch(\PDO::FETCH_ASSOC);

					if ($data['emblem_len'] > 0)
						$ebm = $data['emblem_data'];
				}
			}
			catch (\PDOException $e)
			{
				$ebm = null;
			}

			$cache = true;
			if( !is_null($ebm) )
			{
				$ebm = gzuncompress(pack('H*', $ebm));
				$im = $this->imagecreatefrombmpstring($ebm);
			}
			else
			{
				$im = imagecreatefrompng(getcwd().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'no_ebm.png');
				$cache = false;
			}


			header($this->header);
			imagepng($im);

			if (ob_get_length() > 0 && $cache == true)
			{
				imagepng($im, '../jcp/cache/'.sha1($_SERVER['REQUEST_URI']).'.png');
				//file_put_contents('../jcp/cache/'.sha1($_SERVER['REQUEST_URI']).'.png', ob_get_contents());
				ob_end_flush();
			}

			imagedestroy($im);
			exit;
		}

		public function __construct(array $data)
		{
			$this->page_string = '';
			$this->show_page = false;
			$this->header = 'Content-Type: image/png';

			$this->guild_id = intval($data['id']);
		}
		/*
		 * 	Adapted from:
			emblem.php v0.1 - By MagicalTux & LebrEf -
			Idea : LebrEf
			Code : MagicalTux (90%) - LebrEf (10%)

			Updates: evos
			> palette load, fread(); crashed the script
			> 8bits images were a pixel off to the right

		*/
		public function imagecreatefrombmpstring($im)
		{
			//so that ide type guys don't bitch like crazy..
			$type = $size = $reserved = $offset = $width = $height = $planes = $bits = $compression = $imagesize = $xrest = $yres = $ncolor = $important = 0;
			$header = unpack("vtype/Vsize/v2reserved/Voffset", substr($im,0,14));
			$info = unpack("Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant", substr($im,14,40));

			extract($info);
			extract($header);

			if($type != 0x4D42) return false; // signature "BM"

			$palette_size = $offset - 54;

			// $ncolor = $palette_size / 4;
			//	$gd_header = "";
			//	// true-color vs. palette
			//	$gd_header .= ($palette_size == 0) ? "\xFF\xFE" : "\xFF\xFF";
			//	$gd_header .= pack("n2", $width, $height);
			//	$gd_header .= ($palette_size == 0) ? "\x01" : "\x00";
			//	if($palette_size) $gd_header .= pack("n", $ncolor);
			//	// no transparency
			//	$gd_header .= "\xFF\xFF\xFF\xFF";

			$imres=imagecreatetruecolor($width,$height);
			imagealphablending($imres,false);
			imagesavealpha($imres,true);
			$pal=array();

			if($palette_size) {
				$palette = substr($im, 54, $palette_size);
			//	$gd_palette = "";
				$j = 0; $n=0;
				while($j < $palette_size) {
					$b = ord($palette{$j++});
					$g = ord($palette{$j++});
					$r = ord($palette{$j++});
					$a = ord($palette{$j++});
					//if ( ($r==255) && ($g==0) && ($b==255)) $a=127; // alpha = 255 on 0xFF00FF
					if ( ($r>=240) && ($g<=15) && ($b>=240)) $a=127; // alpha = 255 on 0xFF00FF
					$pal[$n++]=imagecolorallocatealpha($imres, $r, $g, $b, $a);
				}
			}

			$scan_line_size = (($bits * $width) + 7) >> 3;
			$scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size & 0x03): 0;

			for($i = 0, $l = $height - 1; $i < $height; $i++, $l--) {
				// BMP stores scan lines starting from bottom
				$scan_line = substr($im, $offset + (($scan_line_size + $scan_line_align) * $l), $scan_line_size);
				if($bits == 24) {
					$j = 0; $n=0;
					while($j < $scan_line_size) {
						$b = ord($scan_line{$j++});
						$g = ord($scan_line{$j++});
						$r = ord($scan_line{$j++});
						$a = 0;
						//if ( ($r==255) && ($g==0) && ($b==255)) $a=127; // alpha = 255 on 0xFF00FF
						if ( ($r>=240) && ($g<=15) && ($b>=240)) $a=127; // alpha is >= 0xF0 on red and blue
						$col=imagecolorallocatealpha($imres, $r, $g, $b, $a);
						imagesetpixel($imres, $n++, $i, $col);
					}
				}
				else if($bits == 8) {
					$j=0;
					while($j<$scan_line_size) {
						$col=$pal[ord($scan_line{$j++})];
						imagesetpixel($imres, $j-1, $i, $col);
					}
				}
				else if($bits == 4) {
					$j = 0; $n=0;
					while($j < $scan_line_size) {
						$byte = ord($scan_line{$j++});
						$p1 = $byte >> 4;
						$p2 = $byte & 0x0F;
						imagesetpixel($imres, $n++, $i, $pal[$p1]);
						imagesetpixel($imres, $n++, $i, $pal[$p2]);
					}
				}
				else if($bits == 1) {
					$j = 0; $n=0;
					while($j < $scan_line_size) {
						$byte = ord($scan_line{$j++});
						$p1 = (int) (($byte & 0x80) != 0);
						$p2 = (int) (($byte & 0x40) != 0);
						$p3 = (int) (($byte & 0x20) != 0);
						$p4 = (int) (($byte & 0x10) != 0);
						$p5 = (int) (($byte & 0x08) != 0);
						$p6 = (int) (($byte & 0x04) != 0);
						$p7 = (int) (($byte & 0x02) != 0);
						$p8 = (int) (($byte & 0x01) != 0);
						imagesetpixel($imres, $n++, $i, $pal[$p1]);
						imagesetpixel($imres, $n++, $i, $pal[$p2]);
						imagesetpixel($imres, $n++, $i, $pal[$p3]);
						imagesetpixel($imres, $n++, $i, $pal[$p4]);
						imagesetpixel($imres, $n++, $i, $pal[$p5]);
						imagesetpixel($imres, $n++, $i, $pal[$p6]);
						imagesetpixel($imres, $n++, $i, $pal[$p7]);
						imagesetpixel($imres, $n++, $i, $pal[$p8]);
					}
				}
			}
			return $imres;
		}


	}

}