// src/components/Header.jsx
import { useState, useEffect, useRef } from 'react';
import { UserCircle, LogOut, Settings, Search, X } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { useAlert } from '../layout/Alert';

export default function Header({ onSearch, user = {} }) {
  const [open, setOpen] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const dropdownRef = useRef(null);
  const searchRef = useRef(null);
  const navigate = useNavigate();
  const alert = useAlert()

  // Close dropdown when clicking outside
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
        setOpen(false);
      }
      if (searchRef.current && !searchRef.current.contains(event.target)) {
        setSearchOpen(false);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const handleSearch = (e) => {
    e.preventDefault();
    onSearch(searchQuery);
  };

  return (
    <header className="h-16 px-4 md:px-6 flex items-center justify-between bg-white border-b border-neutral-200 sticky top-0 z-10">
      {/* Logo and Mobile Menu Button */}
      <div className="flex items-center gap-4" onClick={() => { alert("A", "Aaaaa!") }}>
        <div className="text-xl font-semibold text-blue-600">Crocodic</div>
      </div>

      {/* Search Bar */}
      <div className="flex-1 max-w-2xl mx-4">
        <form onSubmit={handleSearch} className="relative" ref={searchRef}>
          {searchOpen ? (
            <div className="flex items-center bg-gray-100 rounded-full px-3 py-1">
              <Search className="w-4 h-4 text-gray-500 mr-2" />
              <input
                type="text"
                placeholder="Search project or team member..."
                className="bg-transparent w-full focus:outline-none text-sm"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                autoFocus
              />
              <button
                type="button"
                className="ml-2 text-gray-500 hover:text-gray-700"
                onClick={() => {
                  setSearchOpen(false);
                  setSearchQuery('');
                  onSearch('');
                }}
              >
                <X className="w-4 h-4" />
              </button>
            </div>
          ) : (
            <button
              type="button"
              className="flex items-center gap-2 text-gray-500 hover:text-gray-700"
              onClick={() => setSearchOpen(true)}
            >
              <Search className="w-4 h-4" />
              <span className="hidden md:inline text-sm">Search project</span>
            </button>
          )}
        </form>
      </div>

      {/* User Info */}
      <div className="flex items-center gap-4 relative" ref={dropdownRef}>
        <div 
          className="flex items-center gap-2 cursor-pointer rounded-full p-1 pl-3.5"
          onClick={() => setOpen(!open)}
        >
          <div className="text-right hidden md:block">
            <div className="text-sm font-medium">Arizeta</div>
            <div className="text-xs text-gray-500">Admin</div>
          </div>
          <div className="w-8 h-8 md:w-10 md:h-10 rounded-full bg-blue-100 flex items-center justify-center">
            <UserCircle className="text-blue-600 w-5 h-5 md:w-6 md:h-6" />
          </div>
        </div>

        {/* Dropdown */}
        {open && (
          <div className="absolute top-14 right-0 bg-white border border-neutral-200 rounded-lg shadow-lg w-48 z-20 overflow-hidden">
            <div className="p-3 px-4 border-b border-neutral-200">
              <div className="text-sm font-medium">Arizeta</div>
              <div className="text-xs text-gray-500">freyacarol@email.com</div>
            </div>
            <button
              className="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 flex items-center gap-2 cursor-pointer"
              onClick={() => {
                navigate('/profile/edit');
                setOpen(false);
              }}
            >
              <Settings className="w-4 h-4" /><span>Edit Profile</span>
            </button>
            <button
              className="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 flex items-center gap-2 cursor-pointer text-red-600"
              onClick={() => {
                localStorage.clear();
                navigate('/login');
              }}
            >
              <LogOut className="w-4 h-4" /><span>Logout</span>
            </button>
          </div>
        )}
      </div>
    </header>
  );
} 