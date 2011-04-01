<?php

/// @brief Interface for reading data.
/**
 * This is implemented by StringReader and FileReader to allow the 
 * main c2ephp classes to read data from strings and files with a 
 * consistent and simple OO interface. \n
 * IReaders work a bit like file handles: they store a position
 * and all their functions are based around that position.
 */

interface IReader {
    /// @brief Reads an integer
    /**
     * @param $length Length of the integer in bytes.
     */
    public function ReadInt($length);

    /// @brief Reads a string
    /**
     * @param $length Length of the string to read in bytes
     */
    public function Read($length);

    /// @brief Gets a substring
    /**
     * This function is to allow things like PRAYFile to pull out a
     * big chunk of data and then initialise a new StringReader to
     * deal with it. It's not the most efficient way of doing things
     * memory-wise, but it simplifies the code and c2e files don't
     * tend to get big enough to make this inefficiency a concern on
     * any reasonably modern hardware.
     */
    public function GetSubString($start,$length=FALSE);
    /// @brief Gets the position of the cursor
    /**
     * This is analagous to ftell in C or PHP.
     */
    public function GetPosition();
    /// @brief Changes the current position in the reader's stream
    /**
     * This is analagous to fseek in C or PHP.
     */
    public function Seek($position); 
    /// @brief Advances the position of the reader by $count.
    public function Skip($count);
    /// @brief Reads a c-style string at the current position.
    /**
     * C-style means that the string is terminated by a NUL (0) 
     * character.
     */
    public function ReadCString(); //read a string of unknown length until the first NUL
}
?>
