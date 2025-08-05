// src/components/Layout.jsx
import { Outlet } from 'react-router-dom';
import Sidebar from './Sidebar'
import Header from './Header'

export default function Layout() {
  return (
    <div className="flex h-screen bg-gray-100">
      <div className="w-full flex">
        <Sidebar />
        <main className='pl-[80px] w-full'>
          <Header />
          <Outlet />
        </main>
      </div>
    </div>
  );
}
