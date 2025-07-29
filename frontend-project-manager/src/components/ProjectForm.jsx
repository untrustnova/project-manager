import { useState } from 'react';

export default function ProjectForm({ onCancel, onCreate }) {
  const [form, setForm] = useState({
    name: '',
    start: '',
    end: '',
    level: 'Medium',
    about: '',
    director: '',
    roles: {
      web: '',
      android: '',
      ios: '',
      uiux: '',
      tester: '',
      analyst: '',
      content: '',
      copywriter: '',
    },
  });

  const handleChange = (field, value) => {
    setForm({ ...form, [field]: value });
  };

  const handleRoleChange = (role, value) => {
    setForm({ ...form, roles: { ...form.roles, [role]: value } });
  };

  return (
    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
      {/* Left - Project Info */}
      <div className="bg-white p-6 rounded-lg border">
        <h2 className="text-lg font-semibold mb-4">New Project</h2>

        <div className="space-y-4">
          <input
            type="text"
            placeholder="Project name..."
            value={form.name}
            onChange={(e) => handleChange('name', e.target.value)}
            className="w-full border px-4 py-2 rounded bg-gray-50"
          />

          <div className="flex gap-4">
            <input
              type="date"
              value={form.start}
              onChange={(e) => handleChange('start', e.target.value)}
              className="w-full border px-4 py-2 rounded bg-gray-50"
            />
            <input
              type="date"
              value={form.end}
              onChange={(e) => handleChange('end', e.target.value)}
              className="w-full border px-4 py-2 rounded bg-gray-50"
            />
          </div>

          <select
            value={form.level}
            onChange={(e) => handleChange('level', e.target.value)}
            className="w-full border px-4 py-2 rounded bg-gray-50 text-gray-700"
          >
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
          </select>

          <textarea
            placeholder="about project..."
            rows={3}
            value={form.about}
            onChange={(e) => handleChange('about', e.target.value)}
            className="w-full border px-4 py-2 rounded bg-gray-50"
          />
        </div>
      </div>

      {/* Right - SDM */}
      <div className="bg-white p-6 rounded-lg border">
        <h2 className="text-lg font-semibold mb-4">SDM</h2>
        <div className="grid grid-cols-2 gap-4">
          <select
            className="col-span-2 border px-4 py-2 rounded bg-gray-50"
            value={form.director}
            onChange={(e) => handleChange('director', e.target.value)}
          >
            <option value="">Select Project Director</option>
            <option value="Dodi Saputra">Dodi Saputra</option>
            <option value="Athena Cyntia">Athena Cyntia</option>
          </select>

          <select
            value={form.roles.web}
            onChange={(e) => handleRoleChange('web', e.target.value)}
            className="border px-4 py-2 rounded bg-gray-50"
          >
            <option value="">Engineer Web</option>
            <option value="Bagas">Bagas</option>
          </select>
          <select
            value={form.roles.analyst}
            onChange={(e) => handleRoleChange('analyst', e.target.value)}
            className="border px-4 py-2 rounded bg-gray-50"
          >
            <option value="">Analis</option>
            <option value="Septian">Septian</option>
          </select>

          <select
            value={form.roles.android}
            onChange={(e) => handleRoleChange('android', e.target.value)}
            className="border px-4 py-2 rounded bg-gray-50"
          >
            <option value="">Engineer Android</option>
            <option value="Ahmad">Ahmad</option>
          </select>
          <select
            value={form.roles.content}
            onChange={(e) => handleRoleChange('content', e.target.value)}
            className="border px-4 py-2 rounded bg-gray-50"
          >
            <option value="">Content Creator</option>
            <option value="Bobi">Bobi</option>
          </select>

          <select
            value={form.roles.ios}
            onChange={(e) => handleRoleChange('ios', e.target.value)}
            className="border px-4 py-2 rounded bg-gray-50"
          >
            <option value="">Engineer IOS</option>
            <option value="Riko">Riko</option>
          </select>
          <select
            value={form.roles.copywriter}
            onChange={(e) => handleRoleChange('copywriter', e.target.value)}
            className="border px-4 py-2 rounded bg-gray-50"
          >
            <option value="">Copywriter</option>
            <option value="Agus">Agus</option>
          </select>

          <select
            value={form.roles.uiux}
            onChange={(e) => handleRoleChange('uiux', e.target.value)}
            className="border px-4 py-2 rounded bg-gray-50"
          >
            <option value="">UI/UX</option>
            <option value="Riko">Riko</option>
          </select>
          <select
            value={form.roles.tester}
            onChange={(e) => handleRoleChange('tester', e.target.value)}
            className="border px-4 py-2 rounded bg-gray-50"
          >
            <option value="">Tester</option>
            <option value="Rani">Rani</option>
          </select>
        </div>
      </div>

      <div className="flex justify-end gap-3 pt-6">
        <button onClick={onCancel} className="border px-6 py-2 rounded">
          Cancel
        </button>
        <button
          onClick={() => onCreate(form)}
          className="bg-black text-white px-6 py-2 rounded"
        >
          Create
        </button>
      </div>
    </div>
  );
}
