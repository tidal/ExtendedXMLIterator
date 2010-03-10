<?php
/*
	ExtendedXMLIterator
	
	Copyright (c) 2006-2010 Timo Michna
	
	Permission is hereby granted, free of charge, to any person obtaining
	a copy of this software and associated documentation files (the
	"Software"), to deal in the Software without restriction, including
	without limitation the rights to use, copy, modify, merge, publish,
	distribute, sublicense, and/or sell copies of the Software, and to
	permit persons to whom the Software is furnished to do so, subject to
	the following conditions:
	The above copyright notice and this permission notice shall be
	included in all copies or substantial portions of the Software.
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
	LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
	OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
	WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	@author     	Timo Michna <timomichna@yahoo.de>
	@copyright  	Copyright (c) 2006-2010, Timo Michna 
	@version    	@package_version@
	@package 		XML_Util 
	@since         	File available since version 0.1.0	
*/

/*
 * Class ExtendedXMLIterator
 *
 * Copyright (c) 2006-2010 Timo Michna
 *
 * Util and Convinience Class to extend PHP´s native SimpleXMLIterator Class
 * Extended API offers shortcut methods for common XML Traversing tasks.
 * (Note that this class initially has been written in and for PHP5.0.x, so some of 
 * of the methods might [not must] be obsolete for later PHP versions.)
 * @author     	Timo Michna <timomichna@yahoo.de>
 * @copyright  	Copyright (c) 2006-2010, Timo Michna 
 * @version    	@package_version@
 * @package 	XML_Util
 * @since       Class available since version 0.1.0
 */

class ExtendedXMLIterator extends SimpleXMLIterator {
	/**
	* Returns first result of an XPath-Expression
	* as Single-Node
	*
	* @name xpathSingle
	* @access public
	* @param string XPath-Expression
	* @return mixed result of XPath-Expression as XML-String
	*/
	public function xpathSingle($xpath){
		$ret = $this->xpath($xpath);
		return (isset($ret[0])) ? ($ret[0]) : false;
	}
	
	/**
	* Returns result of an XPath-Expression as XML
	* Optionally wraps XML-String in Root-Node.
	*
	* @name showXpathXML
	* @access public
	* @param string XPath-Expression
	* @param mixed Root-Node name to wrap XML-String in or true to use default
	* @return string result of XPath-Expression as XML-String
	*/
	public function xpathXML($xpath, $root_node = false){
		$res = $this->xpath($xpath);
		$xml = '';
		if(is_array($res)){
			foreach($res as $r){
				$xml .= $r->asXMLString();
			}
			return self::__wrapRootNode($xml, $root_node);
		}
	}
	
	/**
	* Returns result of a XPath-Expression as XML
	* formatted for being displayed in Browser.
	* Optionally wraps XML-String in Root-Node.
	*
	* @name showXpathXML
	* @access public
	* @param string XPath-Expression
	* @param string Root-Node to wrap XML-String in
	* @return string result of XPath-Expression as XML-String
	*/
	public function showXpathXML($xpath, $root_node = false){
		return htmlspecialchars($this->xpathXML($xpath, $root_node));
	}
	
	/**
	* Returns childs in a given Range.
	*
	* @name childsRange
	* @access public
	* @param integer start-position
	* @param integer end-position
	* @return array Childs in Range
	*/
	public function childsRange($postition_start, $position_end){
		return $this->xpath(self::childsRangeXpath($postition_start, $position_end));
	}
	
	/**
	* Returns childs in a given Range as XML.
	* Optionally wraps XML-String in Root-Node.
	*
	* @name childsRangeXML
	* @access public
	* @param integer start-position
	* @param integer end-position
	* @param string Root-Node to wrap XML-String in
	* @return string Childs in Range as XML
	*/
	public function childsRangeXML($postition_start, $position_end, $root_node = false){
		return $this->xpathXML(self::childsRangeXpath($postition_start, $position_end), $root_node);
	}
	
	/**
	* Returns XPath-Expression for methods
	* childsFromTo and childsFromToXML
	*
	* @name getChildsFromToXpath
	* @access protected
	* @param integer start-position
	* @param integer end-position
	* @return string XPath-Expression
	*/
	protected static function childsRangeXpath($postition_start, $position_end){
		return 'self::*/*[position() >='.$postition_start.' and position()<='.$position_end.']';
	}
	
	/**
	* Returns node as XML-Tags with 
	* or without (default) XML-declaration
	*
	* @name xml
	* @access public
	* @param bool add XML-Declaration?
	* @return string XML-String
	*/
	public function asXMLString($xml_tag = false){
		// note that PHP only supports XML version 1.0, while there
		// is also a relativly unknown XML version 1.1 
		return (!$xml_tag) ? str_replace('<?xml version="1.0"?>', '', $this->asXML()) : $this->asXML();
	}
	
	/**
	* returns XML in a browser printbable way
	*
	* @name showXML
	* @access public
	* @param bool add XML-Declaration?
	* @return object attributes as object
	*/
	public function showXML($xml_tag = false){
		return htmlspecialchars($this->asXMLString($xml_tag));
	}
	
	/**
	* Returns child-node at given position
	*
	* @name childAtPosition
	* @access public
	* @param integer posistion of child-node
	* @return object first child-node
	*/
	public function childAtPosition($postition){
		return $this->xpathSingle('self::*/*[position() = '.$postition.']');
	}
	
	/**
	* Returns first child-node
	*
	* @name firstChild
	* @access public
	* @param string namespace of child
	* @return object first child-node
	*/
	public function firstChild($namespace = false){
		$childs = $this->children($namespace);
		return (isset($childs[0])) ? ($childs[0]) : false;
	}
	
	/**
	* Returns last child-node
	*
	* @name lastChild
	* @access public
	* @param string namespace of child
	* @return object last child-node
	*/
	public function lastChild($namespace = false){
		$childs = $this->children($namespace);
		return $childs[(count($childs)-1)];
	}
	
	/**
	* returns children as XML
	*
	* @name childrenXML
	* @access public
	* @param mixed Root-Node name to wrap XML-String in or true to use default
	* @return string children as XML
	*/
	public function childrenXML($root_node = false){
		$xml = '';
		foreach($this->children() as $child){
			$xml .= $child->asXML();
		}
		return self::__wrapRootNode($xml, $root_node);
	}
	
	/**
	* returns current child-node as XML
	*
	* @name currentXML
	* @access public
	* @param bool add XML-Declaration?
	* @return string current child-node as XML
	*
	*/
	public function currentXML($xml_tag = false){
		return $this->current()->asXMLString($xml_tag);
	}
	
	/**
	* returns children of current child-node
	*
	* @name currentChilds
	* @access public
	* @param string namespace of the children
	* @return object children of current child-node
	*/
	
	public function currentChildren($namespace = false){
		return $this->current()->children($namespace);
	}
	
	/**
	* returns attributes as object
	*
	* @name Attr
	* @access public
	* @return object attributes as object
	*/
	public function showCurrentXML(){
		return htmlspecialchars($this->currentXML());
	}
	
	
	/**
	* returns childnode with given id
	* (Note that this method is not equivalent to XML DOM´s 
	* getElementById where id attributes have to be declared 
	* in an schema)
	*
	* @name nodeById
	* @access public
	* @param string id of node to be returned
	* @return object returns node type as given in 'type'
	*/
	public function nodeById($id){
		return $this->xpathSingle(self::__elemsByAttrXpath('id', $id));
	}
	
	/**
	* checks wether node with given id exists
	*
	* @name nodeById
	* @access public
	* @param string id of node to be returned
	* @return object returns node type as given in 'type'
	*/
	public function idExists($id){
		if($id){
			return ($this->xpathSingle(self::__elemsByAttrXpath('id', $id))) ? true : false;
		}
	}
	
	/**
	* returns childnodes with given attribute and
	* optionally given atribute value.
	*
	* @name elemsByAttr
	* @access public
	* @param string attribute name
	* @param string attribute value
	* @return array
	*
	*/
	public function nodesByAttribute($attr, $value = false){
		return $this->xpath(self::__elemsByAttrXpath($attr, $value));
	}
	
	protected static function __elemsByAttrXpath($attr, $value = false){
		return ($value) ? "//*[@$attr=\"$value\"]" : "//*[@$attr]";
	}

	protected static function __wrapRootNode($xml, $root_node){
		if($root_node !== false){
			$root_node = (is_string($root_node)) ? $root_node : 'root';
			$xml = '<'.$root_node.'>'.$xml.'</'.$root_node.'>';
		}
		return $xml;		
	}
	
}
?>
