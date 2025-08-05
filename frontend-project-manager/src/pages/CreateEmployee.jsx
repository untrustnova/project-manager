import { useState } from 'react';
import { User, Mail, Lock, ShieldCheck } from 'lucide-react';

export default function CreateEmployee() {
  const [form, setForm] = useState({
    username: '',
    email: '',
    password: '',
    confirmPassword: '',
    division: '',
  });

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = () => {
    if (!form.username || !form.email || !form.password || form.password !== form.confirmPassword) {
      alert('Form tidak lengkap atau password tidak cocok!');
      return;
    }

    console.log('Submitted:', form);
    alert('Account created successfully');
  };

  const renderInput = (label, name, type = 'text', Icon) => (
    <div className="relative">
      <label className="block font-medium mb-1">{label}</label>
      <div className="relative">
        {Icon && <Icon className="absolute left-3 top-3 text-gray-400 w-4 h-4" />}
        <input
          name={name}
          type={type}
          value={form[name]}
          onChange={handleChange}
          className="w-full border border-neutral-200 px-10 py-2 rounded bg-gray-50"
          placeholder={label}
        />
      </div>
    </div>
  );

  return (
    <div className="p-8 flex justify-center">
      <div className="bg-white rounded-lg shadow-md p-8 w-full max-w-4xl">
        <h2 className="text-2xl font-semibold mb-6">Create New Account</h2>

        <div className="grid grid-cols-2 gap-6">
          {renderInput("Username", "username", "text", User)}
          {renderInput("Password", "password", "password", Lock)}
          {renderInput("Email", "email", "email", Mail)}
          {renderInput("Confirm Password", "confirmPassword", "password", Lock)}

          <div className="col-span-2">
            <label className="block font-medium mb-1">Division</label>
            <div className="relative">
              <ShieldCheck className="absolute left-3 top-3 text-gray-400 w-4 h-4" />
              <select
                name="division"
                value={form.division}
                onChange={handleChange}
                className="w-full border border-neutral-200 px-10 py-2 rounded bg-gray-50"
              >
                <option value="">Enter division</option>
                <option value="Analis">Analis</option>
                <option value="Backend Developer">Backend Developer</option>
                <option value="Front End Developer">Front End Developer</option>
                <option value="UI/UX Designer">UI/UX Designer</option>
              </select>
            </div>
          </div>
        </div>

        <div className="flex justify-center mt-8">
          <button
            onClick={handleSubmit}
            className="bg-black text-white px-8 py-2 rounded hover:opacity-90"
          >
            Create Account
          </button>
        </div>
      </div>
    </div>
  );
}
