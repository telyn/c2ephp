<?php
/* 0 = Main directory 
1 = Sounds directory 
2 = Images directory 
3 = Genetics Directory 
4 = Body Data Directory (ATT files) 
5 = Overlay Directory 
6 = Backgrounds Directory 
7 = Catalogue Directory 
8 = Bootstrap Directory (Denied) 
9 = Worlds Directory (Denied) 
10 = Creatures Directory 
11 = Pray Files Directory (Denied) */

/**
 * @relates PrayDependency
 * @name Pray Dependency Types
 *
 * All the possible types of Pray Dependency.
 */
///@{
/// Value: 1
define('PRAY_DEPENDENCY_SOUND',1);
/// Value: 2
define('PRAY_DEPENDENCY_IMAGE',2);
/// Value: 3
define('PRAY_DEPENDENCY_GENE',3);
/// Value: 4
define('PRAY_DEPENDENCY_BODYDATA',4);
/// Value: 5
define('PRAY_DEPENDENCY_OVERLAY',5);
/// Value: 6
define('PRAY_DEPENDENCY_BACKGROUND',6);
/// Value: 7
define('PRAY_DEPENDENCY_CATALOGUE',7);
/// Value: 10
define('PRAY_DEPENDENCY_CREATURE',10);
//@}

/// @brief Dependency class used in various PrayBlocks.
/**
 * Known classes that use it: AGNTBlock, DSAGBlock, EGGSBlock
 */
class PrayDependency {
    /// @cond INTERNAL_DOCS
    
	private $category;
    private $filename;

    /// @endcond
    
    /// @brief Initialise a new PrayDependency
    /**
     * @param $category One of the PRAY_DEPENDENCY_* constants
     * @param $filename The name of the file this dependency relates to.
     */
	public function PrayDependency($category,$filename) {
		$this->category = $category;
		$this->filename = $filename;
	}
	public function GetCategory() {
		return $this->category;
	}
	public function GetFileName() {
		return $this->filename;
	}
}
?>
