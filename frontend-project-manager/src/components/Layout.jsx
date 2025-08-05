// src/components/Layout.jsx
import { Outlet } from 'react-router-dom';
import Sidebar from './Sidebar'
import Header from './Header'
import { useAuthorization } from '../layout/Authorization';

export default function Layout() {
  const authorization = useAuthorization()

  console.log(authorization)

  return <>
    <div className="flex h-screen bg-gray-100">
      <div className="w-full flex">
        <Sidebar />
        <main className='pl-[80px] w-full'>
          <Header user={authorization.userauth?.data||{}}/>
          <Outlet />
        </main>
      </div>
    </div>
  </>
}
