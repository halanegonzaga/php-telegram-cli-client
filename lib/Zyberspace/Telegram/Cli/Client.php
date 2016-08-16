<?php

/**
 * Copyright 2015 Eric Enold <zyberspace@zyberware.org>
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace Zyberspace\Telegram\Cli;

/**
 * php-client for telegram-cli.
 * If you don't need the command-wrappers in this class or want to make your own, use the RawClient-class. :)
 */
class Client extends RawClient {

    /**
     * Sets status as online.
     *
     * @return boolean true on success, false otherwise
     *
     * @uses exec()
     */
    public function setStatusOnline() {
        return $this->exec('status_online');
    }

    /**
     * Sets status as offline.
     *
     * @return boolean true on success, false otherwise
     *
     * @uses exec()
     */
    public function setStatusOffline() {
        return $this->exec('status_offline');
    }

    /**
     * Sends a typing notification to $peer.
     * Lasts a couple of seconds or till you send a message (whatever happens first).
     *
     * @param string $peer The peer, gets escaped with escapePeer()
     * @param int $range 
     * 0 - none ok
     * 1 - typing ok
     * 2 - cancel ok
     * 3 - record video ok
     * 4 - upload video x
     * 5 - record audio ok
     * 6 - upload audio x
     * 7 - upload photo x
     * 8 - upload document x
     * 9 - geo ok
     * 10 - choose contact ok
     *
     * @return boolean true on success,se false otherwise
     */
    public function sendTyping($peer, $range = 1) {
        return $this->exec('send_typing ' . $this->escapePeer($peer) . ' ' . $range);
    }

    /**
     * Sends a text message to $peer.
     *
     * @param string $peer The peer, gets escaped with escapePeer()
     * @param string $msg The message to send, gets escaped with escapeStringArgument()
     *
     * @return boolean true on success, false otherwise
     *
     * @uses exec()
     * @uses escapePeer()
     * @uses escapeStringArgument()
     */
    public function msg($peer, $msg) {
        $peer = $this->escapePeer($peer);
        $msg = $this->escapeStringArgument($msg);
        return $this->exec('msg ' . $peer . ' ' . $msg);
    }

    /**
     * Sends a text message to several users at once.
     *
     * @param array $userList List of users / contacts that shall receive the message,
     *                        gets formated with formatPeerList()
     * @param string $msg The message to send, gets escaped with escapeStringArgument()
     *
     * @return boolean true on success, false otherwise
     */
    public function broadcast(array $userList, $msg) {
        return $this->exec('broadcast ' . $this->formatPeerList($userList) . ' '
                        . $this->escapeStringArgument($msg));
    }

    /**
     * Creates a new group chat with the users in $userList.
     *
     * @param string $chatTitle The title of the new chat
     * @param array $userList The users you want to add to the chat. Gets formatted with formatPeerList().
     *                        The current telgram-user (who creates the chat) will be added automatically.
     *
     * @return boolean true on success, false otherwise
     *
     * @uses exec()
     * @uses escapeStringArgument()
     * @uses formatPeerList()
     */
    public function createGroupChat($chatTitle, $userList) {
        if (count($userList) <= 0) {
            return false;
        }

        return $this->exec('create_group_chat', $this->escapeStringArgument($chatTitle), $this->formatPeerList($userList));
    }

    /**
     * Returns an info-object about a chat (title, name, members, admin, etc.).
     *
     * @param string $chat The name of the chat (not the title). Gets escaped with escapePeer().
     *
     * @return object|boolean A chat-object; false if somethings goes wrong
     *
     * @uses exec()
     * @uses escapePeer()
     */
    public function chatInfo($chat) {
        return $this->exec('chat_info', $this->escapePeer($chat));
    }

    /**
     * Renames a chat. Both, the chat title and the print-name will change.
     *
     * @param string $chat The name of the chat (not the title). Gets escaped with escapePeer().
     * @param string $chatTitle The new title of the chat.
     *
     * @return boolean true on success, false otherwise
     *
     * @uses exec()
     * @uses escapePeer()
     * @uses escapeStringArgument()
     */
    public function renameChat($chat, $newChatTitle) {
        return $this->exec('rename_chat', $this->escapePeer($chat), $this->escapeStringArgument($newChatTitle));
    }

    /**
     * Adds a user to a chat.
     *
     * @param string $chat The chat you want the user to add to. Gets escaped with escapePeer().
     * @param string $user The user you want to add. Gets escaped with escapePeer().
     * @param int $numberOfMessagesToFoward The number of last messages of the chat, the new user should see.
     *                                      Default is 100.
     *
     * @return boolean true on success, false otherwise
     *
     * @uses exec()
     * @uses escapePeer()
     */
    public function chatAddUser($chat, $user, $numberOfMessagesToFoward = 100) {
        return $this->exec('chat_add_user', $this->escapePeer($chat), $this->escapePeer($user), (int) $numberOfMessagesToFoward);
    }

    /**
     * Deletes a user from a chat.
     *
     * @param string $chat The chat you want the user to delete from. Gets escaped with escapePeer().
     * @param string $user The user you want to delete. Gets escaped with escapePeer().
     *
     * @return boolean true on success, false otherwise
     *
     * @uses exec()
     * @uses escapePeer()
     */
    public function chatDeleteUser($chat, $user) {
        return $this->exec('chat_del_user', $this->escapePeer($chat), $this->escapePeer($user));
    }

    /**
     * Sets the profile name
     *
     * @param $firstName The first name
     * @param $lastName The last name
     *
     * @return object|boolean Your new user-info as an object; false if somethings goes wrong
     *
     * @uses exec()
     */
    public function setProfileName($firstName, $lastName) {
        return $this->exec('set_profile_name ' . $this->escapeStringArgument($firstName) . ' '
                        . $this->escapeStringArgument($lastName));
    }

    /**
     * Adds a user to the contact list
     *
     * @param string $phoneNumber The phone-number of the new contact, needs to be a telegram-user.
     *                            Every char that is not a number gets deleted, so you don't need to care about spaces,
     *                            '+' and so on.
     * @param string $firstName The first name of the new contact
     * @param string $lastName The last name of the new contact
     *
     * @return object|boolean The new contact-info as an object; false if somethings goes wrong
     *
     * @uses exec()
     * @uses escapeStringArgument()
     */
    public function addContact($phoneNumber, $firstName, $lastName) {
        $phoneNumber = preg_replace('%[^0-9]%', '', (string) $phoneNumber);
        if (empty($phoneNumber)) {
            return false;
        }

        return $this->exec('add_contact ' . $phoneNumber . ' ' . $this->escapeStringArgument($firstName)
                        . ' ' . $this->escapeStringArgument($lastName));
    }

    /**
     * Renames a user in the contact list
     *
     * @param string $contact The contact, gets escaped with escapePeer()
     * @param string $firstName The new first name for the contact
     * @param string $lastName The new last name for the contact
     *
     * @return object|boolean The new contact-info as an object; false if somethings goes wrong
     *
     * @uses exec()
     * @uses escapeStringArgument()
     */
    public function renameContact($contact, $firstName, $lastName) {
        return $this->exec('rename_contact ' . $this->escapePeer($contact)
                        . ' ' . $this->escapeStringArgument($firstName) . ' ' . $this->escapeStringArgument($lastName));
    }

    /**
     * Deletes a contact.
     *
     * @param string $contact The contact, gets escaped with escapePeer()
     *
     * @return boolean true on success, false otherwise
     *
     * @uses exec()
     * @uses escapePeer()
     */
    public function deleteContact($contact) {
        return $this->exec('del_contact ' . $this->escapePeer($contact));
    }

    /**
     * Blocks a user .
     *
     * @param string $user The user, gets escaped with escapePeer()
     *
     * @return boolean true on success, false otherwise
     *
     * @uses exec()
     * @uses escapePeer()
     */
    public function blockUser($user) {
        return $this->exec('block_user ' . $this->escapePeer($user));
    }

    /**
     * Unblocks a user.
     *
     * @param string $user The user, gets escaped with escapePeer()
     *
     * @return boolean true on success, false otherwise
     *
     * @uses exec()
     * @uses escapePeer()
     */
    public function unblockUser($user) {
        return $this->exec('unblock_user ' . $this->escapePeer($user));
    }

    /**
     * Marks all messages with $peer as read.
     *
     * @param string $peer The peer, gets escaped with escapePeer()
     *
     * @return boolean true on success, false otherwise
     *
     * @uses exec()
     * @uses escapePeer()
     */
    public function markRead($peer) {
        return $this->exec('mark_read ' . $this->escapePeer($peer));
    }

    /**
     * Returns an array of all contacts. Every contact is an object like it gets returned from `getUserInfo()`.
     *
     * @return array|boolean An array with your contacts as objects; false if somethings goes wrong
     *
     * @uses exec()
     *
     * @see getUserInfo()
     */
    public function getContactList() {
        return $this->exec('contact_list');
    }

    /**
     * Return array search usernames
     *
     * @return array|boolean 
     *
     * @uses exec()
     *
     */
    public function getContactSearch($username) {
        return $this->exec('contact_search ' . trim($username));
    }

    /**
     * Returns the informations about the user as an object.
     *  Prints info about user (id, last online, phone)
     *
     * @param string $user The user, gets escaped with escapePeer()
     *
     * @return object|boolean An object with informations about the user; false if somethings goes wrong
     *
     * @uses exec()
     * @uses escapePeer()
     */
    public function getUserInfo($user) {
        return $this->exec('user_info ' . $this->escapePeer($user));
    }

    /**
     * Returns an array of all your dialogs. Every dialog is an object with type "user" or "chat".
     *
     * @return array|boolean An array with your dialogs; false if somethings goes wrong
     *
     * @uses exec()
     *
     * @see getUserInfo()
     */
    public function getDialogList() {
        return $this->exec('dialog_list');
    }

    /**
     * Returns an array of your past message with that $peer. Every message is an object which provides informations
     * about it's type, sender, retriever and so one.
     * All messages will also be marked as read.
     *
     * @param string $peer The peer, gets escaped with escapePeer()
     * @param int $limit (optional) Limit answer to $limit messages. If not set, there is no limit.
     * @param int $offset (optional) Use this with the $limit parameter to go through older messages.
     *                    Can also be negative.
     *
     * @return array|boolean An array with your past messages with that $peer; false if somethings goes wrong
     *
     * @uses exec()
     * @uses escapePeer()
     */
    public function getHistory($peer, $limit = null, $offset = null) {
        if ($limit !== null) {
            $limit = (int) $limit;
            if ($limit < 1) { //if limit is lesser than 1, telegram-cli crashes
                $limit = 1;
            }
            $limit = ' ' . $limit;
        } else {
            $limit = '';
        }
        if ($offset !== null) {
            $offset = ' ' . (int) $offset;
        } else {
            $offset = '';
        }

        return $this->exec('history ' . $this->escapePeer($peer) . $limit . $offset);
    }

    /**
     * Send file to peer
     *
     * @param  string $peer The peer, gets escaped with escapePeer()
     * @param  string $path The file path, gets formatted with formatFileName()
     * @return boolean
     *
     * @uses exec()
     * @uses escapePeer()
     * @uses formatFileName()
     */
    public function sendFile($peer, $path) {
        $peer = $this->escapePeer($peer);
        $formattedPath = $this->formatFileName($path);

        return $this->exec('send_file ' . $peer . ' ' . $formattedPath);
    }

    /**
     * Get Updates Session
     * @return 
     */
    public function getUpdates() {
        return $this->exec('main_session ');
    }

    public function safeQuit() {
        return $this->exec('safe_quit ');
    }

    /**
     * Delete Contact
     * @param type $peer
     * @return type
     */
    public function setDeleteContact($peer) {
        return $this->exec("del_contact " . $peer);
    }

    /**
     * Get Info Profile
     * @return type
     */
    public function getSelf() {
        return $this->exec("get_self");
    }

    /**
     * Send Photo
     * @param type $peer
     * @param type $path
     * @return type
     */
    public function sendPicture($peer, $path, $caption = null) {
        $peer = $this->escapePeer($peer);
        $formattedPath = $this->formatFileName($path);

        return $this->exec('send_photo ' . $peer . ' ' . $formattedPath . ' ' . $this->escapeStringArgument($caption));
    }

    /**
     * Send Video
     * @param type $peer
     * @param type $path
     * @return type
     */
    public function sendVideo($peer, $path, $caption = null) {
        $peer = $this->escapePeer($peer);
        $formattedPath = $this->formatFileName($path);

        return $this->exec('send_video ' . $peer . ' ' . $formattedPath . ' ' . $this->escapeStringArgument($caption));
    }

    /**
     * 
     * @param type $peer
     * @param type $path
     * @return type
     */
    public function sendAudio($peer, $path) {
        $peer = $this->escapePeer($peer);
        $formattedPath = $this->formatFileName($path);

        return $this->exec('send_audio ' . $peer . ' ' . $formattedPath);
    }

    /**
     * Send Document
     * @param string $peer
     * @param string $path
     * @return 
     */
    public function sendDocument($peer, $path) {
        $peer = $this->escapePeer($peer);
        $formattedPath = $this->formatFileName($path);

        return $this->exec('send_document ' . $peer . ' ' . $formattedPath);
    }

    /**
     * Send Location
     * @param string $peer
     * @param string $lat
     * @param string $lon
     * @return 
     */
    public function sendLocation($peer, $lat, $lon) {
        $peer = $this->escapePeer($peer);

        return $this->exec('send_location ' . $peer . ' ' . $lat . ' ' . $lon);
    }

    /**
     * Send VCard
     * @param string $peer
     * @param string $phone
     * @param string $first
     * @param string $last
     * @return 
     */
    public function sendContato($peer, $phone, $first, $last) {
        $peer = $this->escapePeer($peer);
        return $this->exec('send_contact ' . $peer . ' ' . $this->escapeStringArgument($phone) . ' ' . $this->escapeStringArgument($first) . ' ' . $this->escapeStringArgument($last));
    }

    /**
     * Set Photo Profile
     * @param file $path
     * @return 
     */
    public function setProfilePhoto($path) {
        $formattedPath = $this->formatFileName($path);
        return $this->exec('set_profile_photo ' . $formattedPath);
    }

    /**
     * Set Username for Profile
     * @param string $peer 
     * @param string $username
     * @return 
     */
    public function setUsername($username) {
        return $this->exec('set_username ' . $username);
    }

    /**
     * Set @username for Profile
     * @param string $peer 
     * @param string $username
     * @return 
     */
    public function getPhotoUser($peer) {
        return $this->exec('view_user_photo ' . $this->escapePeer($peer));
    }

    /**
     * Download Document
     * @return 
     */
    public function loadDocument($msid) {
        return $this->exec('load_document ' . $msid);
    }

    /**
     * Download Photo
     * @return 
     */
    public function loadPhoto($msid) {
        return $this->exec('load_photo ' . $msid);
    }

    /**
     * Create Channel
     * @param type $name
     * @param type $about
     */
    public function createChannel($name, $about) {
        return $this->exec("create_channel " . $this->escapeStringArgument($name) . " " . $this->escapeStringArgument($about));
    }

    /**
     * List of Channels
     * @return type
     */
    public function channelsList() {
        return $this->exec("channel_list ");
    }

    /**
     * Info of Channel
     * @param type $channel
     * @return type
     */
    public function infoChannel($channel) {
        if (!empty($channel)) {
            return $this->exec("channel_info " . $this->escapePeer($channel));
        }
    }

    /**
     * Set Profile Photo: channel_set_photo <channel> <filename>
     * @param type $channel
     * @param type $file
     */
    public function setProfilePhotoChannel($channel, $file) {
        return $this->exec("channel_set_photo " . $this->escapePeer($channel) . " " . $this->formatFileName($file));
    }

    /**
     * Set Username: channel_set_username <channel> <username> 
     * @param type $channel
     * @param type $username
     */
    public function setUsernameChannel($channel, $username) {
        return $this->exec("channel_set_username " . $this->escapePeer($channel) . " " . $this->escapeStringArgument($username));
    }

    /**
     * Export Link Channel: export_channel_link <channel>
     * @param type $channel
     * @return type
     */
    public function exportLinkChannel($channel) {
        return $this->exec("export_channel_link " . $this->escapePeer($channel));
    }

    /**
     * Rename Channel: rename_channel <channel> <new name>
     * @param type $channel
     * @param type $rename
     */
    public function renameChannel($channel, $rename) {
        return $this->exec("rename_channel " . $this->escapePeer($channel) . " " . $this->escapeStringArgument($rename));
    }

    /**
     * Set About Channel: channel_set_about <channel> <about>
     * @param type $channel
     * @param type $about
     * @return type
     */
    public function setAboutChannel($channel, $about) {
        return $this->exec("channel_set_about " . $this->escapePeer($channel) . " " . $this->escapeStringArgument($about));
    }

    /**
     * Delete Msg by ID
     * @param type $msg
     * @return type
     */
    public function deleteMsg($msg) {
        return $this->exec("delete_msg $msg");
    }

    /**
     * Get Admins
     * @param type $channel
     * @return type
     */
    public function getAdmins($channel) {
        return $this->exec("channel_get_admins " . $this->escapePeer($channel));
    }

    /**
     * Get Members
     * @param type $channel
     * @return type
     */
    public function getMembers($channel) {
        return $this->exec("channel_get_members  " . $this->escapePeer($channel));
    }

    /**
     * Invite Contact: channel_invite <channel> <user>
     * @param type $peer
     * @return type
     */
    public function inviteContact($channel, $peer) {
        return $this->exec("channel_invite " . $this->escapePeer($channel) . " " . $this->escapePeer($peer));
    }

    /**
     * Set Admin Channel: channel_set_admin <channel> <admin> <type> {0 - User , 1 - Moderador, 2 - editor }
     * @param type $channel
     * @param type $peer
     * @param type $tipo
     * @return type
     */
    public function setAdmin($channel, $peer, $tipo) {
        return $this->exec("channel_set_admin " . $this->escapePeer($channel) . " " . $this->escapePeer($peer) . " " . $tipo);
    }

    /**
     * Kick Contact: channel_kick <channel> <user>
     * @param type $channel
     * @param type $peer
     * @return type
     */
    public function kickContact($channel, $peer) {
        return $this->exec("channel_kick " . $this->escapePeer($channel) . " " . $this->escapePeer($peer));
    }

}
