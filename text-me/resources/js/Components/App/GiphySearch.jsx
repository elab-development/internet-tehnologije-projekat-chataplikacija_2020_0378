import { useState } from "react";
import axios from "axios";
import { Dialog, DialogPanel, DialogTitle, Transition } from '@headlessui/react';
import { Fragment } from 'react';
import 'daisyui/dist/full.css'; // Importuj DaisyUI stilove
import { XMarkIcon } from "@heroicons/react/24/solid";

const GiphySearch = ({ onGifSelect }) => {
    const [searchTerm, setSearchTerm] = useState("");
    const [gifs, setGifs] = useState([]);
    const [isOpen, setIsOpen] = useState(false);

    const searchGiphy = async (term) => {
        const apiKey = "dlMeoqlKVcKe1CIr9Z1kjCjkk87YIVIE";  // Postavi svoj pravi API ključ

        try {
            const response = await axios.get(
                `https://api.giphy.com/v1/gifs/search?api_key=${apiKey}&q=${encodeURIComponent(term)}&limit=10`
            );
            setGifs(response.data.data);
            setIsOpen(true);
        } catch (error) {
            console.error("Error fetching GIFs from Giphy API", error);
        }
    };

    const handleSearch = (e) => {
        e.preventDefault();
        searchGiphy(searchTerm);
    };


    // const handleGifClick = (gifUrl) => {
    //     onGifSelect(gifUrl);  // Pozovite funkciju za selektovanje GIF-a
    // };

    return (
        <div className="relative">
            <form onSubmit={handleSearch} className="flex-1 items-center">
                <input
                    type="text"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    placeholder="Search for GIFs"
                    className="input input-bordered min-w-[50px] max-w-xs mb-2"
                />
                <button
                    type="submit"
                    className="btn glass rounded-l-none"
                >
                    Search
                </button>
            </form>

            <Transition appear show={isOpen} as={Fragment}>
                <Dialog as="div" className="fixed inset-0 flex items-center justify-center z-50" onClose={() => setIsOpen(false)}>
                    <DialogPanel className="max-w-lg w-full p-4 bg-white rounded shadow-lg relative">
                    <button 
                        className="absolute top-2 right-2 text-gray-500 hover:text-gray-700  z-50"
                        onClick={() => setIsOpen(false)}
                    >
                    <XMarkIcon className="h-6 w-6" aria-hidden="true" />
                    </button>
                        <DialogTitle as="h3" className="text-lg font-bold mb-2">Select a GIF</DialogTitle>
                        <div className="grid grid-cols-2 gap-4 max-h-60 overflow-auto">
                            {gifs.map((gif) => (
                                <img
                                    key={gif.id}
                                    src={gif.images.fixed_height.url}
                                    alt={gif.title}
                                    className="cursor-pointer border rounded"
                                    onClick={() => {
                                        onGifSelect(gif.images.fixed_height.url);
                                        setIsOpen(false);
                                    }}
                                />
                            ))}
                        </div>
                    </DialogPanel>
                </Dialog>
            </Transition>
        </div>
    );
};

export default GiphySearch;
