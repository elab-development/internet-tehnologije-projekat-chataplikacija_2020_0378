
import ConversationItem from "@/Components/App/ConversationItem";
import TextInput from "@/Components/TextInput";
import { PencilSquareIcon } from "@heroicons/react/24/solid";
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


    const onSearch = (ev) => {
        const search = ev.target.value.toLowerCase();
        //pratimo lokalne konv i updateujemo sortirane dole preko useeffecta
        //filtriramo konv prema imenu konv ili grupe
        setLocalConversations(
            conversations.filter((conversation) => {
                return (
                    conversation.name.toLowerCase().includes(search) 
                    
                );
            })
        );
    }

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
    },[localConversations]);


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

    //Elemeniti ce biti rasporedjeni preko flex-a, maksimalna sirina
    //Prvi div unutar roditeljskog se odnosi na chatove sa leve strane i responzivnost(male srednej i velike ekrane)
    //Ternarni za selected conversation se odnosii na mobilni ml je margine left
    return (
        <>
           <div className ="flex-1 w-full flex overflow-hidden" >

            <div className={`transition-all w-full sm:w-[220px] md:w-[300px] bg-slate-800 flex flex-col overflow-hidden
                ${selectedConversation ? "-ml-[100%] sm:ml-0" : ""               
                    }`}
            >

                    <div className="flex items-center justify-between py-2 px-3 text-xl font-medium text-gray-200">
                        My Conversations 
                        <div
                        className="tooltip tooltip-left"
                        data-tip="Create new Group"
                        >
                            <button 
                            className="text-gray-400 hover:text-gray-200">
                                <PencilSquareIcon className="size-6 text-blue-500" />
                            </button>
                        </div>
                    </div>
            
                    <div className="p-3">
                        <TextInput
                            onKeyUp={onSearch}
                            placeholder="Filter users and groups"
                            className="w-full"
                        />
                    </div>

                    <div className="flex-1 overflow-auto">
                        {sortedConversations && sortedConversations.map((conversation) => (
                          <ConversationItem
                            key={`${conversation.is_group ? "group_" : "user_"}${conversation.id}`} 
                            conversation={conversation}
                            online={!!isUserOnline(conversation.id)}
                            selectedConversation={selectedConversation}
                          />  
                        ) )}
                    </div>
            </div>
            <div className="flex-1 flex flex-col overflow-hidden">
                {children}
            </div>

           </div>
        </>
    );
}

export default ChatLayout;