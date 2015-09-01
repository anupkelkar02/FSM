 
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/core/site_admin_controller'.EXT);
//require_once(APPPATH.'/controllers/admin/staff_call'.EXT);

class Proposal extends Site_admin_controller
{	
	public function insert_data2()
	{
		redirect('http://localhost/FSM-master/index.php/admin/home/proposal');
	}
	public function insert_data()
	{	
		
		
		/*to send data into database*/
					
			if(isset($_POST['save']))
			 {
					
					
					$_POST["item_price"];
					 $tota = array_sum($_POST["item_price"]);
					
					
					
					echo"oki";
					$d1=implode(", ",$_POST["item_index"]);
					$d2=implode(", ",$_POST["item_name"]);
					$d3=implode(", ",$_POST["item_price"]);
					$data = array(
						
						'service4' => $d1,
						'price4' => $d2,
						'rate4' => $tota,
						
						
						
						'ref1' => $_POST['ref1'],
						'date1' => $_POST['date1'],
						'address1' => $_POST['address1'],
						'name1' => $_POST['name1'],
						'subject1' => $_POST['subject1'],
						'desc1' => $_POST['desc1'],
						
						'service1' => $_POST['service1'],
						'service2' => $_POST['service2'],
						'service3' => $_POST['service3'],
						
						'price1' => $_POST['price1'],
						'price2' => $_POST['price2'],
						'price3' => $_POST['price3'],
						
						'rate1' => $_POST['rate1'],
						'rate2' => $_POST['rate2'],
						'rate3' => $_POST['rate3'],
						
						'note1' => $_POST['note1'],
						'note2' => $_POST['note2'],
						'note3' => $_POST['note3'],
						'note4' => $_POST['note4'],
						'note5' => $_POST['note5'],
						'note6' => $_POST['note6'],
						
						'whyyou' => $_POST['whyyou'],
						'stu' => $_POST['stu'],
						'loca' => $_POST['loca'],
						'site' => $_POST['site'],
						//'hitech' => $_POST['hitech'],
						'company' => $_POST['company'],
						'mobile' => $_POST['mobile'],
						'gps' => $_POST['gps'],
						'hightechguard' => $_POST['hightechguard'],
						'metropolis' => $_POST['metropolis'],
						
						'guardwith1' => $_POST['guardwith1'],
						'guardwith2' => $_POST['guardwith2'],
						'guardwith3' => $_POST['guardwith3'],
						'guardwith4' => $_POST['guardwith4'],
						'guardwith5' => $_POST['guardwith5'],
						'guardwith6' => $_POST['guardwith6'],
						'guardwith7' => $_POST['guardwith7'],
						'guardwith8' => $_POST['guardwith8'],
						
						'equipment1' => $_POST['equipment1'],
						'equipment2' => $_POST['equipment2'],
						'equipment3' => $_POST['equipment3'],
						'equipment4' => $_POST['equipment4'],
						'equipment5' => $_POST['equipment5'],
						'equipment6' => $_POST['equipment6'],
						'equipment7' => $_POST['equipment7'],
						
						'workmen' => $_POST['workmen'],
						'police1' => $_POST['police1'],
						'ministry1' => $_POST['ministry1'],
						'professional' => $_POST['professional'],
						'last' => $_POST['last'],
						'lname' => $_POST['lname'],
						'signature' => $_POST['signature'],
						'date2' => $_POST['date2']
						);
						$this->db->insert('grs_proposal', $data);
		echo "ok";
		/*to creat eword file*/
		$this->load->library('word');
		//our docx will have 'lanscape' paper orientation
		$section = $this->word->createSection(array('orientation'=>'landscape'));
		 
		$this->word->addFontStyle('rStyle', array('italic'=>true,'color'=>'gray' ,'size'=>12));
		$this->word->addParagraphStyle('pStyle', array('align'=>'center', 'spaceAfter'=>90));
		
		$section->addTextBreak(1);
		//$section->addImage(FCPATH.'/image/_mars.jpg', array('width'=>100, 'height'=>100, 'align'=>'right'));
		
		// Add text elements
		$section->addText("Our Ref No:".$_POST['ref1'], array('name'=>'Tahoma', 'size'=>11));
		$section->addText(" ".$_POST['date1'], array('name'=>'Tahoma', 'size'=>11));
		$this->word->addParagraphStyle('pStyle', array('align'=>'left', 'spaceAfter'=>0));
		$section->addText(" ".$_POST['address1'], array('name'=>'Arial' , 'size'=>11));
		$section->addText("Dear Mr/Ms:".$_POST['name1']);			
		$section->addText("SUB:".$_POST['subject1'], array('name'=>'Tahoma', 'size'=>11));
		$section->addText($_POST['desc1'], array('name'=>'Verdana'));
		$section->addTextBreak(1);
				

		// Define table style arrays
		$styleTable = array('borderSize'=>2,  'cellMargin'=>80);
		$styleFirstRow = array('bold'=>true,'size'=>18);
				
		// Define cell style arrays
		$styleCell = array('valign'=>'center');
		$styleCellBTLR = array('valign'=>'center', 'textDirection'=>PHPWord_Style_Cell::TEXT_DIR_BTLR);
				
		// Define font style for first row
		$fontStyle = array('bold'=>true, 'align'=>'center');
				
		// Add table style
		$this->word->addTableStyle('myOwnTableStyle', $styleTable, $styleFirstRow);
				
		// Add table
		$table = $section->addTable('myOwnTableStyle');
				
		// Add row
		$table->addRow(900);				
		// Add cells
		$table->addCell(500, $styleCell)->addText('S/N', $fontStyle);
		$table->addCell(3500, $styleCell)->addText('Service Description', $fontStyle);
		$table->addCell(3500, $styleCell)->addText('Unit Price($)', $fontStyle);
		$table->addCell(3500, $styleCell)->addText('Monthly Rate($)', $fontStyle);
		//$table->addCell(500, $styleCellBTLR)->addText('Row 5', $fontStyle);
				
		// Add more rows / cells
		$table->addRow();
			$table->addCell(500)->addText("1");
			$table->addCell(2000)->addText($_POST['service1']);
			$table->addCell(2000)->addText($_POST['price1']);
			$table->addCell(300)->addText($_POST['rate1']);
		$table->addRow();
			$table->addCell(900)->addText("2");
			$table->addCell(2000)->addText($_POST['service2']);
			$table->addCell(2000)->addText($_POST['price2']);
			$table->addCell(300)->addText($_POST['rate2']);
		$table->addRow();
			$table->addCell(900)->addText("3");
			$table->addCell(2000)->addText($_POST['service3']);
			$table->addCell(2000)->addText($_POST['price3']);
			$table->addCell(300)->addText($_POST['rate3']);
			$itemCount = count($_POST["item_name"]);
		for($i = 0; $i <= $itemCount; $i++) {
		$table->addRow();
			$table->addCell(900)->addText("3");
			$table->addCell(2000)->addText($_POST["item_index"][$i]);
			$table->addCell(2000)->addText($_POST["item_name"][$i]);
			$table->addCell(300)->addText($_POST["item_price"][$i]);
			
		}
		
		$table->addRow();						
			$table->addCell(3500, $styleCell)->addText('Total');
			$table->addCell(3500, $styleCell)->addText();
			$table->addCell(3500, $styleCell)->addText();
			//for($i = 0; $i <= $itemCount; $i++) 
			//{	
			$table->addCell(3500, $styleCell)->addText($_POST['rate1']+$_POST['rate2']+$_POST['rate3']+$tota);
			//}
			

		
		
		
		$section->addTextBreak(1);
		$section->addText("Important Note:", array('bold'=>true,'italic'=>true,'name'=>'Tahoma', 'size'=>10));
		$section->addText("Note1:".$_POST['note1'], array('bold'=>true,'name'=>'Tahoma', 'size'=>9));
		$section->addText("Note2:".$_POST['note2'], array('bold'=>true,'name'=>'Tahoma', 'size'=>9));
		$section->addText("Note3:".$_POST['note3'], array('bold'=>true,'name'=>'Tahoma', 'size'=>9));
		$section->addText("Note4:".$_POST['note4'], array('bold'=>true,'name'=>'Tahoma', 'size'=>9));
		$section->addText("Note5:".$_POST['note5'], array('bold'=>true,'name'=>'Tahoma', 'size'=>9));
		$section->addText("Note6:".$_POST['note6'], array('bold'=>true,'name'=>'Tahoma', 'size'=>9));
		
		$section->addText("3. Why You Should Consider Us:", array('bold'=>true,'name'=>'Tahoma', 'size'=>10));
		$section->addText("    ".$_POST['whyyou'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("   a. Organizational Structure:", array('bold'=>true,'name'=>'Tahoma', 'size'=>10));
		$section->addText("    ".$_POST['stu'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("   b. Location and accountability of HR/Payroll teams and Ops teams:", array('bold'=>true,'name'=>'Tahoma', 'size'=>10));
		$section->addText("    ".$_POST['loca'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("   c. Site inspections conducted by 24 Hours Operations Centre crew:", array('bold'=>true,'name'=>'Tahoma', 'size'=>10));
		$section->addText("    ".$_POST['site'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("   d.	High Tech Resources:", array('bold'=>true,'name'=>'Tahoma', 'size'=>10));
		//$section->addText("    ".$_POST['hitech'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("     	i)Company Vans:", array('bold'=>true,'name'=>'Tahoma', 'size'=>10)); 
		$section->addText("           ".$_POST['company'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("     	ii)Mobile apps and Email on the go:", array('bold'=>true,'name'=>'Tahoma', 'size'=>10));
		$section->addText("           ".$_POST['mobile'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("     	iii)GPS Tracking:", array('bold'=>true,'name'=>'Tahoma', 'size'=>10));
		$section->addText("           ".$_POST['gps'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("     	iv)High Tech guardroom:", array('bold'=>true,'name'=>'Tahoma', 'size'=>10));
		$section->addText("           ".$_POST['hightechguard'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("   e.	Metropolis Welfare:", array('bold'=>true,'name'=>'Tahoma', 'size'=>10));
		$section->addText("    ".$_POST['metropolis'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("   we provide the guard with:", array('name'=>'Tahoma', 'size'=>10));
		$section->addText("    i) ".$_POST['guardwith1'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("    ii) ".$_POST['guardwith2'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("    iii) ".$_POST['guardwith3'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("    iv) ".$_POST['guardwith4'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("    v) ".$_POST['guardwith5'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("    vi) ".$_POST['guardwith6'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("    vii) ".$_POST['guardwith7'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("    viii) ".$_POST['guardwith8'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("   f.	Proper Equipping and Essential Equipment:", array('bold'=>true,'name'=>'Tahoma', 'size'=>10));
		$section->addText("    i) ".$_POST['equipment1'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("    ii) ".$_POST['equipment2'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("    iii) ".$_POST['equipment3'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("    iv) ".$_POST['equipment4'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("    v) ".$_POST['equipment5'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("    vi) ".$_POST['equipment6'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("    vii) ".$_POST['equipment7'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("   g.	Workmen Compensation and Public Liability Insurance:", array('bold'=>true,'name'=>'Tahoma', 'size'=>10));
		$section->addText("    ".$_POST['workmen'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("  h.	Compliance with Regulatory Requirements. :", array('bold'=>true,'name'=>'Tahoma', 'size'=>10));
		$section->addText("     	i)Police Licensing Regulatory Department:", array('bold'=>true,'name'=>'Tahoma', 'size'=>10)); 
		$section->addText("           ".$_POST['police1'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("     	ii)Ministry of Manpower Exemption from Overtime:", array('bold'=>true,'name'=>'Tahoma', 'size'=>10));
		$section->addText("           ".$_POST['ministry1'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("  i.	Professional Affiliation:", array('bold'=>true,'name'=>'Tahoma', 'size'=>10));
		$section->addText("           ".$_POST['professional'], array('name'=>'Tahoma', 'size'=>10));
		$section->addText("    4. ".$_POST['last'], array('name'=>'Tahoma', 'size'=>10));
		$section->addTextBreak(2);
		$section->addText("  Yours Sincerely                                                                                         
		 Agreed & Confirmed By", array('name'=>'Tahoma', 'size'=>11));
		$section->addText("    ".$_POST['lname']."    
		
		
		
		
		
		              ".$_POST['signature'], array('name'=>'Tahoma', 'size'=>11));
		$section->addText("  Director                                                                                         
		           Signature & Co’ Stamp", array('name'=>'Tahoma', 'size'=>11));
		$section->addText("    "."    
		
		
		
		
		
		              Date: ".$_POST['date2'], array('name'=>'Tahoma', 'size'=>11));	   
		$section->addText("*This is a computer generated quotation and requires no signature.", array('name'=>'Tahoma', 'size'=>10));
		
		
		
		/*file crteation proces start*/
		$filename='Proposal.docx'; //save our document as this file name
		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		 
		$objWriter = PHPWord_IOFactory::createWriter($this->word, 'Word2007');
		$objWriter->save('php://output');
		/*file crteation proces end*/
				}
				
	}
	/*public function propose_data()
	{
	 	echo"hi";
		$this->load->view('admin/proposal_history');
	}*/
	
}
?>