import { usePage } from "@inertiajs/react";
import { useEffect } from "react";



const ChatLayout = ({children}) => {
    const page = usePage();
    const conversations = page.props.conversations;
    const selectedConversation = page.props.selectedConversation;

    console.log("conversations", conversations);
    console.log("selectedConversation", selectedConversation);

    useEffect(() => {
        Echo.join("online")
        //kada se ja pridruzim kanalu dobicu i sve ostale povezane korisnike u clgu
            .here((users) => {
             console.log("here", users);
            })
        //kada se neko drugi pridruzi kanalu on se prikazuje u clgu
            .joining((user) => {
                console.log("joining", user);
            })
        //kada neko napusti kanal prikazace se u clg
            .leaving((user) => {
                console.log("leaving", user);
            })
            .error((error) => {
                console.error("error", error);
            });

        //uz pomoc ovoga pratimo ko je online a ko offline i updateujemo UI

    }, [])

    return (
        <>
            ChatLayout
            <div> {children} </div>
        </>
    );
}

export default ChatLayout;