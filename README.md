# AHQUFileAnaliser
Fileanalyser for Allen Heath Qu-Mixer Scene Files

This is a PHP Script where you can Upload your SCENE Files from your Allen Heath QU Mixer.  
You can find these files on your USB Drive under AHQU/SCENES/SCENEXXX.DAT where XXX is a Number.

# Demo
have a try on qu.davidzingg.ch

# Features
* Channel Number
* Channel Name
* Source
* Phantom Power Enabled / Disabled
* HPF Filter Enabled / Disabled
* Stereo Link
* Assigned Mute Groups
* Assigned DCA Groups

Currently only viewing files is supported. 
There is a 32 Bit checksum in the file, if you change any value you need also to calculate a new checksum. 
I dont know how to calculate these sum. Feel free to find it out / reverse enigneer it!

If you change any value in the File you do it at your own risk! Im not responsible for any Damage on your Mixing Console. 
I dont know what the mixer does if theres a wrong checksum or a value that is not possible.

# Contribution
Feel free to fork and create a pull request with your changes

You can use the diff.php script to compare two files. This script provides you which bytes have changed

All Files have the same size and each value is always on the same position in the file. 
Each channel uses 192 Bytes to store its data. The first byte is the channel number starting with 0. Outputchannels uses 0xff as channel number. 

In The Scenefiles folder are some example files. Band.DAT ist the main file. All other files have only the changes written in the filename to that file.
