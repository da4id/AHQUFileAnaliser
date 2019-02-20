<?php
    class QuFileCore{
        //Definitions
        const SCENE_NAME_POSITION = 0X0C;
        const CHANNEL_OFFSET_BYTES = 0xC0;
        const FIRST_CHANNEL_NUMBER_POSITION = 0xE7;
        const FIRST_CHANNEL_PHANTOM_POSITOIN = 0xCA;
        const FIRST_CHANNEL_NAME_POSITION = 0xCC;
        const FIRST_CHANNEL_HPF_POSITION = 0xA8;
        const FIRST_CHANNEL_SOURCE_BYTE1 = 0xB4;
        const FIRST_CHANNEL_SOURCE_BYTE2 = 0xBB;
        const FIRST_CHANNEL_MUTE_GROUPS = 0xBC;
        const FIRST_CHANNEL_DCA_GROUPS = 0xD6;
        const FIRST_CHANNEL_GROUP_ENABLE = 0xC0;

        private $file;

        function __construct($inputFile) {
            $this->file = $inputFile;
        }

        function getSceneName(){
            return substr($this->file,self::SCENE_NAME_POSITION,14);
        }

        function getChannelByte($i,$byteNumber){
            $byte = $this->file[$byteNumber+$i*self::CHANNEL_OFFSET_BYTES];
            return ord($byte);
        }

        //Functions
        function getChannelNumber($i){
            return $this->getChannelByte($i,self::FIRST_CHANNEL_NUMBER_POSITION);
        }

        function getChannelName($i){
            return substr($this->file, self::FIRST_CHANNEL_NAME_POSITION+$i*self::CHANNEL_OFFSET_BYTES,6);
        }

        function getChannelPhantom($i){
            return ($this->getChannelByte($i,self::FIRST_CHANNEL_PHANTOM_POSITOIN) & 0x01) == 0x01;
        }

        function getChannelSource($i){
            $byte1 = $this->getChannelByte($i,self::FIRST_CHANNEL_SOURCE_BYTE1) & 0x01;
            $byte2 = $this->getChannelByte($i,self::FIRST_CHANNEL_SOURCE_BYTE2) & 0x01;
            if($byte1 == 0x01 && $byte2 == 0x01)
            {
                return "USB";
            }
            if(($byte1 == 0x00 && $byte2 == 0x01))
            {
                return "dSnake";
            }
            return "Local";
        }
            
        function getChannelHPFEnabled($i){
            return ($this->getChannelByte($i,self::FIRST_CHANNEL_HPF_POSITION) & 0x01) == 0x01;
        }

        function getAssignedMuteGroups($i){
            return $this->getChannelByte($i,self::FIRST_CHANNEL_MUTE_GROUPS);
        }

        function getAssignedDcaGroups($i){
            return $this->getChannelByte($i,self::FIRST_CHANNEL_DCA_GROUPS);
        }

        function isChannelLinked($i){
            return $this->getChannelByte($i,self::FIRST_CHANNEL_GROUP_ENABLE) & 0x01;
        }

    }

?>