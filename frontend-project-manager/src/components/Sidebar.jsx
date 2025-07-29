// src/components/Sidebar.jsx
import { useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import {
  LayoutGrid,
  ClipboardList,
  ClipboardCheck,
  Activity,
  BookOpen,
  Shield,
  Menu,
  ChevronRight,
  UserCircle
} from 'lucide-react';

const navItems = [
  { icon: LayoutGrid, label: 'Dashboard', path: '/dashboard' },
  { icon: ClipboardList, label: 'Projects', path: '/projects' },
  { icon: ClipboardCheck, label: 'Tasks', path: '/tasks' },
  { icon: Activity, label: 'Activity', path: '/activity' },
  { icon: BookOpen, label: 'Leave', path: '/leave' },
  { icon: Shield, label: 'User Management', path: '/employee' },
];

export default function Sidebar() {
  const [expanded, setExpanded] = useState(false);
  const navigate = useNavigate();
  const location = useLocation();

  const isActive = (path) => location.pathname.startsWith(path);

  return (
    <aside
      className={`h-screen bg-white border-r flex flex-col py-6 sticky top-0 transition-all duration-300 ease-in-out
        ${expanded ? 'w-56' : 'w-20'}`}
    >
      {/* Expand/Collapse Button */}
      <div className="px-4 flex justify-end mb-6">
        <button
          onClick={() => setExpanded(!expanded)}
          className="p-2 rounded-full hover:bg-gray-100 text-gray-500"
          aria-label={expanded ? 'Collapse sidebar' : 'Expand sidebar'}
        >
          {expanded ? <ChevronRight className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
        </button>
      </div>

      {/* Navigation Items */}
      <nav className="flex-1 space-y-2 px-2">
        {navItems.map((item, index) => {
          const Icon = item.icon;
          const active = isActive(item.path);

          return (
            <button
              key={item.label}
              onClick={() => navigate(item.path)}
              className={`w-full flex items-center gap-3 p-3 rounded-lg transition
                ${active ? 'text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100'}`}
              aria-current={active ? 'page' : undefined}
            >
              <div
                className={`w-10 h-10 flex items-center justify-center rounded-lg
                  ${active ? 'bg-white border border-blue-500' : 'bg-gray-100'}`}
              >
                <Icon className={`w-5 h-5 ${active ? 'text-blue-600' : ''}`} />
              </div>
              {expanded && <span className="text-sm font-medium">{item.label}</span>}
            </button>
          );
        })}
      </nav>

      {/* User Profile (Collapsed) */}
      <div className="px-2 pt-4 border-t mt-auto">
        <div className="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mx-auto">
          <UserCircle className="text-blue-600 w-6 h-6" />
        </div>
        {expanded && (
          <div className="text-center mt-2">
            <p className="text-sm font-semibold">Admin</p>
            <p className="text-xs text-gray-400">Freyaa</p>
          </div>
        )}
      </div>
    </aside>
  );
}
