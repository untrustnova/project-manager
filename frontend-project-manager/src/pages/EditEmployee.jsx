// src/pages/EditEmployee.jsx
import { useParams } from 'react-router-dom';
import { useState } from 'react';
import { Calendar } from 'lucide-react';

export default function EditEmployee() {
  const { id } = useParams();

  const [form, setForm] = useState({
    name: 'Name Example',
    position: 'UI/UX Designer',
    email: 'lorem@email.com',
    password: 'QQQ123',
    nik: '3326778899003',
    status: 'Tetap',
    phone: '0867744666778',
    telegram: 'Loremipsum',
    address: 'Semarang, Jawa tengah',
    birthDate: '1999-01-01',
    joinDate: '2021-01-18',
    education: 'S1 Teknik Informatika',
    photo: null,
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
      setForm((prev) => ({ ...prev, photo: URL.createObjectURL(file) }));
    }
  };

  const handleDeleteImage = () => {
    setForm((prev) => ({ ...prev, photo: null }));
  };

  const handleSubmit = () => {
    console.log('Updating employee:', form);
    alert('Employee updated!');
  };

  return (
    <div className="p-8 flex justify-center">
      <div className="bg-white rounded-lg shadow-md p-8 w-full max-w-5xl">
        <div className="flex gap-6 items-center mb-6">
          <div>
            <img
              src={form.photo || `https://i.pravatar.cc/100?u=${form.name}`}
              alt="Profile"
              className="w-24 h-24 rounded-full object-cover border"
            />
          </div>
          <div className="flex-1">
            <h2 className="text-xl font-bold">{form.name}</h2>
            <p className="text-gray-500">{form.position}</p>
            <div className="flex gap-2 mt-2">
              <label className="bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-1 rounded cursor-pointer">
                Upload Picture
                <input type="file" className="hidden" onChange={handleImageUpload} />
              </label>
              <button
                onClick={handleDeleteImage}
                className="text-sm px-4 py-1 border border-gray-300 rounded hover:bg-gray-100"
              >
                Deleted Picture
              </button>
            </div>
          </div>
        </div>

        <div className="grid grid-cols-2 gap-6">
          <div>
            <label>Email</label>
            <input
              name="email"
              value={form.email}
              onChange={handleChange}
              className="w-full border px-4 py-2 rounded bg-gray-50"
            />
          </div>

          <div>
            <label>Password</label>
            <input
              name="password"
              value={form.password}
              onChange={handleChange}
              className="w-full border px-4 py-2 rounded bg-gray-50"
              type="password"
            />
          </div>

          <div>
            <label>NIK</label>
            <input
              name="nik"
              value={form.nik}
              onChange={handleChange}
              className="w-full border px-4 py-2 rounded bg-gray-50"
            />
          </div>

          <div>
            <label>Link Telegram</label>
            <input
              name="telegram"
              value={form.telegram}
              onChange={handleChange}
              className="w-full border px-4 py-2 rounded bg-gray-50"
            />
          </div>

          <div>
            <label>Status SDM</label>
            <select
              name="status"
              value={form.status}
              onChange={handleChange}
              className="w-full border px-4 py-2 rounded bg-gray-50"
            >
              <option value="Tetap">Tetap</option>
              <option value="Kontrak">Kontrak</option>
            </select>
          </div>

          <div>
            <label>Alamat</label>
            <input
              name="address"
              value={form.address}
              onChange={handleChange}
              className="w-full border px-4 py-2 rounded bg-gray-50"
            />
          </div>

          <div>
            <label>No. HP</label>
            <input
              name="phone"
              value={form.phone}
              onChange={handleChange}
              className="w-full border px-4 py-2 rounded bg-gray-50"
            />
          </div>

          <div>
            <label>Tanggal Lahir</label>
            <div className="relative">
              <Calendar className="absolute left-3 top-3 text-gray-400 w-4 h-4" />
              <input
                type="date"
                name="birthDate"
                value={form.birthDate}
                onChange={handleChange}
                className="w-full border pl-10 py-2 rounded bg-gray-50"
              />
            </div>
          </div>

          <div>
            <label>Tanggal Masuk</label>
            <div className="relative">
              <Calendar className="absolute left-3 top-3 text-gray-400 w-4 h-4" />
              <input
                type="date"
                name="joinDate"
                value={form.joinDate}
                onChange={handleChange}
                className="w-full border pl-10 py-2 rounded bg-gray-50"
              />
            </div>
          </div>

          <div>
            <label>Pendidikan Terakhir</label>
            <input
              name="education"
              value={form.education}
              onChange={handleChange}
              className="w-full border px-4 py-2 rounded bg-gray-50"
            />
          </div>
        </div>

        <div className="mt-8 flex justify-center">
          <button
            onClick={handleSubmit}
            className="bg-black text-white px-8 py-2 rounded hover:opacity-90"
          >
            Simpan
          </button>
        </div>
      </div>
    </div>
  );
}
