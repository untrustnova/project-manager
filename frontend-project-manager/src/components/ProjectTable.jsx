export default function ProjectTable({ projects }) {
  const levelColor = {
    High: 'bg-red-100 text-red-800',
    Medium: 'bg-yellow-100 text-yellow-800',
    Low: 'bg-green-100 text-green-800',
  };

  const statusColor = {
    Running: 'bg-blue-100 text-blue-800',
    Maintenance: 'bg-orange-100 text-orange-800',
    'To do': 'bg-gray-100 text-gray-800',
    Complete: 'bg-green-100 text-green-800',
  };

  return (
    <div className="overflow-x-auto">
      <table className="min-w-full bg-white rounded-lg overflow-hidden">
        <thead className="bg-gray-50">
          <tr className="text-left text-sm font-medium text-gray-700">
            <th className="py-3 px-4">#</th>
            <th className="py-3 px-4">Project Name</th>
            <th className="py-3 px-4">Start Date</th>
            <th className="py-3 px-4">Deadline</th>
            <th className="py-3 px-4">Project Director</th>
            <th className="py-3 px-4">Level</th>
            <th className="py-3 px-4">Status</th>
          </tr>
        </thead>
        <tbody className="divide-y divide-gray-200">
          {projects.map((project, index) => (
            <tr key={project.id} className="hover:bg-gray-50">
              <td className="py-3 px-4 text-gray-700">{index + 1}</td>
              <td className="py-3 px-4 font-medium">{project.name}</td>
              <td className="py-3 px-4 text-gray-600">{project.start}</td>
              <td className="py-3 px-4 text-gray-600">{project.end}</td>
              <td className="py-3 px-4 text-gray-600">{project.director}</td>
              <td className="py-3 px-4">
                <span className={`px-2 py-1 rounded-full text-xs ${levelColor[project.level]}`}>
                  {project.level}
                </span>
              </td>
              <td className="py-3 px-4">
                <span className={`px-2 py-1 rounded-full text-xs ${statusColor[project.status]}`}>
                  {project.status}
                </span>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}