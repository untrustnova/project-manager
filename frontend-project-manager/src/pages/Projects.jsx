import { useState } from 'react';
import { Filter } from 'lucide-react';
import ProjectTable from '../components/ProjectTable';
import ProjectForm from '../components/ProjectForm';

export default function Projects() {
  const [projects, setProjects] = useState([
    {
      id: 1,
      name: "Wordpress Plugin Update",
      start: "Nov 4, 2024",
      end: "Des 25, 2024",
      director: "Athena Cyntia",
      level: "High",
      status: "Running",
    },
    {
      id: 2,
      name: "Wordpress Plugin Update",
      start: "Nov 4, 2024",
      end: "Des 25, 2024",
      director: "Athena Cyntia",
      level: "High",
      status: "Maintenance",
    },
  ]);

  const [showForm, setShowForm] = useState(false);

  const handleToggleForm = () => setShowForm(!showForm);

  const handleCreateProject = (newProject) => {
    setProjects(prev => [
      ...prev,
      {
        id: prev.length + 1,
        ...newProject,
        status: 'To do',
      },
    ]);
    setShowForm(false);
  };

  return (
    <div className="p-6 max-w-6xl mx-auto">
      {/* Page Header */}
      <div className="flex justify-between items-center mb-2">
        <h2 className="text-2xl font-semibold">Projects</h2>
      </div>

      {/* Filter + Create Button */}
      <div className="flex justify-between items-center mb-6">
        {/* Filter Button - Left */}
        <button
          className="flex items-center gap-2 border border-gray-300 bg-white px-4 py-2 rounded text-sm text-gray-700 hover:bg-gray-50 transition"
          onClick={() => console.log('Filter clicked')}
        >
          <Filter className="w-4 h-4 text-gray-500" />
          Filter
        </button>

        {/* Create Button - Right */}
        <button
          onClick={handleToggleForm}
          className="bg-black text-white text-sm px-4 py-2 rounded hover:bg-gray-800 transition"
        >
          {showForm ? '× Cancel' : '+ Create project'}
        </button>
      </div>

      {/* Table or Form */}
      {!showForm ? (
        <ProjectTable projects={projects} />
      ) : (
        <div className="mt-6">
          <ProjectForm onCancel={handleToggleForm} onCreate={handleCreateProject} />
        </div>
      )}
    </div>
  );
}
