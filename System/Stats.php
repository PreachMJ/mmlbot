<?php

/*
 *   This file is part of Geekbot.
 *
 *   Geekbot is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   Geekbot is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with Geekbot.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Geekbot;

class Stats{

    function __construct($message){
        
        //set a few variables

        //get user info from db
        $userData = Database::get('member');
        
        //update stuff
        if(!isset($userData->messages)){
            $userData->messages = 0;
        }
        if(!isset($userData->lastMessage)){
            $userData->lastMessage = null;
        }
        $userData->messages = $userData->messages + 1;
        $userData->lastMessage = date(DATE_RFC2822);

        //put it back into the db
        Database::set('member', $userData);

        //lets do the same for the guild itself
        $guildData = Database::get('guild');
        if(!isset($guildData->messages)){
            $guildData->messages = 0;
        }
        $guildData->messages = $guildData->messages + 1;
        Database::set('guild', $guildData);
    }

    private static function calculateLevel($messages) {
        $total = 0;
        $levels = [];
        for ($i = 1; $i < 100; $i++) {
            $total += floor($i + 300 * pow(2, $i / 7.0));
            $levels[] = floor($total / 16);
        }
        $level = 1;
        foreach ($levels as $l) {
            if ($l < $messages) {
                $level++;
            } else {
                break;
            }
        }
        return $level;
    }

    /**
     * @param int $userID The User ID
     * @return string
     */
    public static function getLastMessage($userID){
        $data = Database::get('member', $userID);
        if(!isset($data->lastMessage)){
            $data->lastMessage = "never";
        }
        return $data->lastMessage;
    }

    /**
     * @param int $userID The User ID
     * @return int
     */
    public static function getAmountOfMessages($userID){
        $data = Database::get('member', $userID);
        if(!isset($data->messages)){
            $data->messages = 0;
        }
        return $data->messages;
    }

    /**
     * @param int $userID The User ID
     * @return string
     */
    public static function getClass($userID){
        $data = Database::get('member', $userID);
        if(!isset($data->class)){
            $data->class = "none";
        }
        return $data->class;
    }

    /**
     * @param int $userID The User ID
     * @return int
     */
    public static function getBadJokes($userID){
        $data = Database::get('member', $userID);
        if(!isset($data->badJokes)){
            $data->badJokes = 0;
        }
        return $data->badJokes;
    }

    /**
     * @param int $userID The User ID
     * @return int
     */
    public static function getLevel($userID){
        $messages = Stats::getAmountOfMessages($userID);
        $level = Stats::calculateLevel($messages);
        return $level;

    }

    /**
     * @return int
     */
    public static function getGuildMessages(){
        $data = Database::get('guild');
        if(!isset($data->messages)){
            $data->messages = 0;
        }
        return $data->messages;
    }

}