<?php
interface IReader {
    public function ReadInt($length); //reads an integer of size $length
    public function Read($length); //reads a $length-character long string
    public function GetSubString($start,$length=FALSE);//gets a SubString of the resource
    public function GetPosition(); //gets the current position of the reader.
    public function Seek($position); //jump to an absolute position in the file
    public function Skip($count); //skip a number of bytes in the reader
    public function ReadCString(); //read a string of unknown length until the first NUL
}
?>