import { useState } from 'react';
import TaskBoard from '../components/TaskBoard';
import TaskTable from '../components/TaskTable';

export default function Tasks() {
  const [view, setView] = useState('board');

  return (
    <div className="p-6 max-w-7xl mx-auto">
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-2xl font-bold">Procode</h2>
        <div className="flex gap-3">
          <button className="px-4 py-2 border rounded-md">Date</button>
          <button className="px-4 py-2 bg-black text-white rounded-md">
            + Create task
          </button>
          <button className="px-4 py-2 border rounded-md">Transfer task</button>
          <button
            onClick={() => setView(view === 'board' ? 'table' : 'board')}
            className="px-4 py-2 border rounded-md"
          >
            {view === 'board' ? 'View All Task' : 'Task Board View'}
          </button>
        </div>
      </div>

      {view === 'board' ? <TaskBoard /> : <TaskTable />}
    </div>
  );
}