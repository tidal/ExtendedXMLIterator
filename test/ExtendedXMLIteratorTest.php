<?php
require_once '../lib/ExtendedXMLIterator.php';

class ExtendedXMLIteratorTest extends PHPUnit_Framework_TestCase {

	protected 
		$xml = false,
		$ns_xml,
		$root;
	
	public function __construct(){		
		$xml = file_get_contents(dirname (__FILE__).'/_files/test.xml');
		if($xml){
			$this->xml = $xml;
		}
		$ns_xml = file_get_contents(dirname (__FILE__).'/_files/namespace.xml');
		if($ns_xml){
			$this->ns_xml = $ns_xml;
		}
	}
		
	protected function setUp(){
		if(!$this->xml){
			$this->markTestSkipped('Could not get XML Doc.');
		}
		$this->root = new ExtendedXMLIterator($this->xml);
		$this->root->rewind();	
	}
	
	public function testXpathSingle(){
		$xpath = '/bookstore/book[1]';
		$exp = $this->root->xpath($xpath);	
		$this->assertEquals($exp[0], $this->root->xpathSingle($xpath));	
	}
	
	public function testXpathXML(){
		$xpath = "//title[@lang='eng']";
		$exp = '<root><title id="title1" lang="eng">Foo</title><title lang="eng">Bar</title></root>';
		$this->assertXmlStringEqualsXmlString($exp, '<root>'.$this->root->xpathXML($xpath).'</root>');
	}	
	
	public function testXpathXMLRootnode(){
		$xpath = "//title[@lang='eng']";
		$exp = '<root><title id="title1" lang="eng">Foo</title><title lang="eng">Bar</title></root>';
		$this->assertXmlStringEqualsXmlString($exp, $this->root->xpathXML($xpath, true));
	}	
	
	public function testXpathXMLNamedRootnode(){
		$xpath = "//title[@lang='eng']";
		$exp = '<foo><title id="title1" lang="eng">Foo</title><title lang="eng">Bar</title></foo>';
		$this->assertXmlStringEqualsXmlString($exp, $this->root->xpathXML($xpath, 'foo'));
	}	
	
	
	public function testShowXpathXML(){
		$xpath = "//title[@lang='eng']";
		$exp = '<title id="title1" lang="eng">Foo</title><title lang="eng">Bar</title>';
		$this->assertEquals(htmlspecialchars($exp), $this->root->showXpathXML($xpath));
	}	
	
	public function testShowXpathXMLRootnode(){
		$xpath = "//title[@lang='eng']";
		$exp = '<root><title id="title1" lang="eng">Foo</title><title lang="eng">Bar</title></root>';
		$this->assertEquals(htmlspecialchars($exp), $this->root->showXpathXML($xpath, true));
	}	
	
	public function testShowXpathXMLNamedRootnode(){
		$xpath = "//title[@lang='eng']";
		$exp = '<foo><title id="title1" lang="eng">Foo</title><title lang="eng">Bar</title></foo>';
		$this->assertEquals(htmlspecialchars($exp), $this->root->showXpathXML($xpath, 'foo'));
	}

	public function testChildsRange(){
		$exp = array(
			new ExtendedXMLIterator('<book><title lang="eng">Bar</title><price>39.95</price></book>'),
			new ExtendedXMLIterator('<book><title lang="de">Fuu</title><price>34.95</price></book>')
		);
		$expults = $this->root->childsRange(2, 3);
		foreach($expults as $x=>$expult){
			$this->assertEquals($exp[$x], $expult);
		}
	}	
	
	public function testChildsRangeXML(){
		$xpath = "//title[@lang='eng']";
		$exp = '<root><book><title lang="eng">Bar</title><price>39.95</price></book><book><title lang="de">Fuu</title><price>34.95</price></book></root>';
		$this->assertXmlStringEqualsXmlString($exp, $this->root->childsRangeXML(2, 3, true));
	}

	public function testChildsAtPosition(){
		$exp = new ExtendedXMLIterator('<book><title lang="eng">Bar</title><price>39.95</price></book>');
		$this->assertEquals($exp, $this->root->childAtPosition(2));
	}		

	public function testFirstChild(){
		$exp = new ExtendedXMLIterator('<book><title id="title1" lang="eng">Foo</title><price>29.99</price></book>');
		$this->assertEquals($exp, $this->root->firstChild());
	}

	public function testLastChild(){
		$exp = new ExtendedXMLIterator('<book><title lang="de">Fuu</title><price>34.95</price></book>');
		$this->assertEquals($exp, $this->root->lastChild());
	}
			
	public function testasXMLString(){
		$exp = str_replace('<?xml version="1.0"?>', '', $this->xml);
		$this->assertXmlStringEqualsXmlString($exp, $this->root->asXMLString(false));	
	}	
	
	public function testShowXML(){
		$this->assertEquals(htmlspecialchars($this->root->asXMLString()), $this->root->showXML());	
	}	

	public function testChildrenXML(){
		$exp = '<foo><title id="title1" lang="eng">Foo</title><price>29.99</price></foo>';
		$this->assertXmlStringEqualsXmlString($exp, '<foo>'.$this->root->book->childrenXML().'</foo>');
	}

	public function testChildrenXMLRootnode(){
		$exp = '<foo><title id="title1" lang="eng">Foo</title><price>29.99</price></foo>';
		$this->assertXmlStringEqualsXmlString($exp, $this->root->book->childrenXML('foo'));
	}	
	
	public function testCurrentXML(){
		while($this->root->valid()){
			$this->assertXmlStringEqualsXmlString($this->root->current()->asXMLString(), $this->root->currentXML());
			$this->root->next();	
		}		
	}	
	
	public function testCurrentChildren(){
		while($this->root->valid()){
			$this->assertEquals($this->root->current()->children(), $this->root->currentChildren());
			$this->root->next();	
		}		
	}	
	
	public function testShowCurrentXML(){
		while($this->root->valid()){
			$this->assertEquals(htmlspecialchars($this->root->current()->asXMLString()), $this->root->showCurrentXML());
			$this->root->next();	
		}		
	}	

	public function testNodeById(){
		$exp = new ExtendedXMLIterator('<title id="title1" lang="eng">Foo</title>');
		$this->assertEquals($exp, $this->root->nodeById('title1'));
		$this->assertEquals($exp, $this->root->book->nodeById('title1'));
	}	
	
	public function testNodesByAttribute(){
		$exp = array(
			new ExtendedXMLIterator('<title id="title1" lang="eng">Foo</title>'),
			new ExtendedXMLIterator('<title lang="eng">Bar</title>'),
			new ExtendedXMLIterator('<title lang="de">Fuu</title>')
		);
		$results = $this->root->nodesByAttribute('lang');
		foreach($results as $x=>$result){
			$this->assertEquals($exp[$x], $result);
		}
	}
	
	public function testNodesByAttributeValue(){
		$exp = array(
			new ExtendedXMLIterator('<title id="title1" lang="eng">Foo</title>'),
			new ExtendedXMLIterator('<title lang="eng">Bar</title>')
		);
		$results = $this->root->nodesByAttribute('lang', 'en');
		foreach($results as $x=>$result){
			$this->assertEquals($exp[$x], $result);
		}
	}
	
	
}