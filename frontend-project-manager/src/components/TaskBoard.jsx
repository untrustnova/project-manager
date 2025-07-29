const dummyTasks = {
  'To do': [
    {
      title: 'Wordpress plugin update',
      company: 'Website Management Company',
      date: 'Nov 4, 2024'
    }
  ],
  'In progress': [
    {
      title: 'Create website design',
      company: 'Tosoku Online Marketplace Application',
      date: 'Nov 4, 2024'
    },
    {
      title: 'Human resources data company',
      company: 'Website Management Company',
      date: 'Nov 15, 2024'
    },
    {
      title: 'Dashboard revision',
      company: 'SiteChat Websites That Bring Your Business to Life',
      date: 'Nov 2, 2024'
    }
  ],
  'Review': [
    {
      title: 'Website menu view',
      company: 'Call-Link Menu Transactions via Website',
      date: 'Nov 4, 2024'
    },
    {
      title: 'Marketplace display menu',
      company: 'Website Management Company',
      date: 'Nov 1, 2024'
    }
  ],
  'Completed': [
    {
      title: 'Create login page',
      company: 'VeriGopher Digital Innovation Without Borders',
      date: 'Nov 4, 2024'
    },
    {
      title: 'Create flowchart',
      company: 'Tosoku Online Marketplace Application',
      date: 'Nov 1, 2024'
    }
  ]
};

export default function TaskBoard() {
  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      {Object.entries(dummyTasks).map(([status, tasks]) => (
        <div key={status} className="space-y-4">
          <h3 className="text-lg font-semibold">{status}</h3>
          <div className="space-y-3">
            {tasks.map((task, idx) => (
              <div key={idx} className="bg-white p-4 rounded-lg shadow border border-gray-200">
                <h4 className="font-medium">{task.title}</h4>
                <p className="text-sm text-gray-500 mt-1">{task.company}</p>
                <p className="text-xs text-gray-400 mt-2">{task.date}</p>
              </div>
            ))}
          </div>
        </div>
      ))}
    </div>
  );
}