import { usePage } from "@inertiajs/react";
import { useEffect, useState } from "react";



const ChatLayout = ({children}) => {
    const page = usePage();
    const conversations = page.props.conversations;
    const selectedConversation = page.props.selectedConversation;

    const [localConversations, setLocalConversations] = useState([]); //bice updateovana localConv kad god je conversations primljena
    const [sortedConversations, setSortedConversations] = useState([]);

    const [onlineUsers, setOnlineUsers] = useState({}); //kad god se neko pridruzi kanalu ubacicemo ga u onlineUsers

    const isUserOnline = (userId) => onlineUsers[userId]; //funkcija koja vraca objekat user ako postoji userId u online users


    console.log("conversations", conversations);
    console.log("selectedConversation", selectedConversation);

    useEffect(() => {
        setSortedConversations(
            localConversations.sort((a, b) => {
                if (a.blocked_at && b.blocked_at) {
                    return a.blocked_at > b.blocked_at ? 1 : -1;
                } else if(a.blocked_at){
                    return 1;
                } else if(b.blocked_at){
                    return -1;
                }
                if (a.last_message_date && b.last_message_date) {
                    return b.last_message_date.localeCompare(
                        a.last_message_date
                    );
                } else if(a.last_message_date){
                    return -1;
                } else if(b.last_message_date){
                    return 1;
                }
                else{
                    return 0;
                }
            })
        );
    },[conversations]);


    //treba da slusamo kad god je konverz promenjena
    useEffect(() => {
        setLocalConversations(conversations);
    },[conversations]);


    useEffect(() => {
        Echo.join("online")
        //kada se ja pridruzim kanalu dobicu i sve ostale povezane korisnike u clgu
            .here((users) => {
                const onlineUsersObj = Object.fromEntries(
                    users.map((user) => [user.id, user])
                );

                setOnlineUsers((prevOnlineUsers) => {
                    return { ...prevOnlineUsers, ...onlineUsersObj };
                });
            })
        //kada se neko drugi pridruzi kanalu on se prikazuje u clgu
            .joining((user) => {
                setOnlineUsers((prevOnlineUsers) => {
                    const updatedUsers = {...prevOnlineUsers};
                    updatedUsers[user.id] = user;
                    return updatedUsers;
                });
            })
        //kada neko napusti kanal prikazace se u clg
            .leaving((user) => {
                setOnlineUsers((prevOnlineUsers) => {
                    const updatedUsers = {...prevOnlineUsers};
                    delete updatedUsers[user.id];
                    return updatedUsers;
            });
        })
            .error((error) => {
                console.error("error", error);
            });

        //uz pomoc ovoga pratimo ko je online a ko offline i updateujemo UI

        return() => {
            Echo.leave("online");
        };

    }, []);


    return (
        <>
           
        </>
    );
}

export default ChatLayout;