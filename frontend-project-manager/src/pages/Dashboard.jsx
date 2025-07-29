import { useState, useEffect } from 'react';
import ErrorBoundary from '../components/ErrorBoundary';
import Sidebar from '../components/Sidebar';
import Header from '../components/Header';
import Tabs from '../components/Tabs';
import EmployeeCard from '../components/EmployeeCard';
import TaskCard from '../components/TaskCard';
import ProjectCard from '../components/ProjectCard';
import ActivityChart from '../components/ActivityChart';
import LoadingSpinner from '../components/LoadingSpinner';
import { employees as localData } from '../data';

export default function Dashboard() {
  const [activeTab, setActiveTab] = useState("Ready");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [employees, setEmployees] = useState({});

  const tabs = ["Ready", "Stand by", "Not ready", "Complete", "Absent"];

  useEffect(() => {
    const loadData = async () => {
      try {
        setLoading(true);
        // Simulasi fetch API
        await new Promise((res) => setTimeout(res, 1000));
        setEmployees(localData);
      } catch (err) {
        setError("Failed to load employee data");
      } finally {
        setLoading(false);
      }
    };

    loadData();
  }, []);

  if (error) {
    return <div className="p-6 text-red-500">Error: {error}</div>;
  }

  return (
    <div className="flex h-screen bg-gray-100">
      <ErrorBoundary>
        <Sidebar />
      </ErrorBoundary>

      <div className="flex-1 flex flex-col overflow-hidden">
        <Header />

        <main className="flex-1 p-6 overflow-auto">
          <div className="flex flex-col lg:flex-row gap-6 h-full">
            <div className="flex-1">
              <Tabs activeTab={activeTab} onChange={setActiveTab} tabs={tabs} />

              {loading ? (
                <LoadingSpinner className="mt-8" />
              ) : (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                  {employees[activeTab]?.length > 0 ? (
                    employees[activeTab].map((employee, index) => (
                      <EmployeeCard
                        key={`${employee.name}-${index}`}
                        data={employee}
                        tab={activeTab}
                      />
                    ))
                  ) : (
                    <div className="col-span-full py-8 text-center text-gray-500">
                      No employees found in "{activeTab}" status
                    </div>
                  )}
                </div>
              )}
            </div>

            <div className="w-full lg:w-96 flex flex-col gap-6">
              <ErrorBoundary><TaskCard /></ErrorBoundary>
              <ErrorBoundary><ProjectCard /></ErrorBoundary>
              <ErrorBoundary><ActivityChart /></ErrorBoundary>
            </div>
          </div>
        </main>
      </div>
    </div>
  );
}

//   useEffect(() => {
//     const loadData = async () => {
//       try {
//         setLoading(true);
//         const data = await fetchEmployees();
//         setEmployees(data);
//       } catch (err) {
//         setError(err.message);
//       } finally {
//         setLoading(false);
//       }
//     };

//     loadData();
//   }, []);

//   if (error) {
//     return <div className="p-6 text-red-500">Error: {error}</div>;
//   }

//   return (
//     <div className="flex h-screen bg-gray-100">
//       <ErrorBoundary>
//         <Sidebar />
//       </ErrorBoundary>
      
//       <div className="flex-1 flex flex-col overflow-hidden">
//         <Header />
        
//         <main className="flex-1 p-6 overflow-auto">
//           <div className="flex flex-col lg:flex-row gap-6 h-full">
//             <div className="flex-1">
//               <Tabs 
//                 activeTab={activeTab} 
//                 onChange={setActiveTab} 
//                 tabs={["Ready", "Not Ready", "Complete", "Online"]} 
//               />
              
//               {loading ? (
//                 <LoadingSpinner className="mt-8" />
//               ) : (
//                 <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
//                   {employees[activeTab]?.length > 0 ? (
//                     employees[activeTab].map((employee) => (
//                       <EmployeeCard 
//                         key={employee.id} 
//                         data={employee} 
//                         tab={activeTab} 
//                       />
//                     ))
//                   ) : (
//                     <div className="col-span-full py-8 text-center text-gray-500">
//                       No employees found in "{activeTab}" status
//                     </div>
//                   )}
//                 </div>
//               )}
//             </div>
  //             <div className="w-full lg:w-96 flex flex-col gap-6">
//               <ErrorBoundary>
//                 <TaskCard />
//               </ErrorBoundary>
              
//               <ErrorBoundary>
//                 <ProjectCard />
//               </ErrorBoundary>
              
//               <ErrorBoundary>
//                 <ActivityChart />
//               </ErrorBoundary>
//             </div>
//           </div>
//         </main>
//       </div>
//     </div>
//   );
// }