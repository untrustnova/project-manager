const tasks = [
  {
    task: 'Wordpress Plugin Update',
    project: 'Website Management Company',
    employee: 'Athena Cyntia',
    status: 'To do',
    created: 'Nov 4, 2024'
  },
  {
    task: 'Create Website Design',
    project: 'Tosoku Online Marketplace Application',
    employee: 'Erika',
    status: 'In progress',
    created: 'Nov 4, 2024'
  },
  {
    task: 'Dashboard Revision',
    project: 'SiteChat Websites That Bring Your Business to Life',
    employee: 'Seon Woo',
    status: 'In progress',
    created: 'Nov 2, 2024'
  },
  {
    task: 'Website Menu View',
    project: 'Call-Link Menu Transactions via Website',
    employee: 'Ashlynn Culhane',
    status: 'Review',
    created: 'Nov 4, 2024'
  }
];

const statusColors = {
  'To do': 'bg-gray-100 text-gray-800',
  'In progress': 'bg-blue-100 text-blue-800',
  'Review': 'bg-yellow-100 text-yellow-800',
  'Completed': 'bg-green-100 text-green-800'
};

export default function TaskTable() {
  return (
    <div className="overflow-x-auto rounded-lg border border-gray-200">
      <table className="min-w-full bg-white">
        <thead className="bg-gray-50">
          <tr className="text-left text-sm font-medium text-gray-700">
            <th className="py-3 px-4">#</th>
            <th className="py-3 px-4">Task Name</th>
            <th className="py-3 px-4">Project</th>
            <th className="py-3 px-4">Assigned Employee</th>
            <th className="py-3 px-4">Status</th>
            <th className="py-3 px-4">Created</th>
          </tr>
        </thead>
        <tbody className="divide-y divide-gray-200">
          {tasks.map((t, i) => (
            <tr key={i} className="hover:bg-gray-50">
              <td className="py-3 px-4">{i + 1}</td>
              <td className="py-3 px-4 font-medium">{t.task}</td>
              <td className="py-3 px-4">{t.project}</td>
              <td className="py-3 px-4">{t.employee}</td>
              <td className="py-3 px-4">
                <span className={`px-2 py-1 rounded-full text-xs ${statusColors[t.status]}`}>
                  {t.status}
                </span>
              </td>
              <td className="py-3 px-4">{t.created}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}