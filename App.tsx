import React, { useState } from 'react';
import { RequestForm } from './components/RequestForm';
import { SettingsModal } from './components/SettingsModal';
import { Phone, Mail, Settings } from 'lucide-react';

const App: React.FC = () => {
  const [isSettingsOpen, setIsSettingsOpen] = useState(false);

  return (
    <div className="min-h-screen bg-slate-50 flex items-center justify-center p-4 md:p-8 relative">
      <button
        onClick={() => setIsSettingsOpen(true)}
        className="fixed top-4 right-4 p-2 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-100 transition-colors z-40"
        aria-label="Nastavenia"
      >
        <Settings size={24} />
      </button>

      <div className="max-w-5xl w-full grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">

        {/* Left Side: Information / Context */}
        <div className="space-y-8 order-2 lg:order-1">
          <div>
            <span className="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold mb-4 tracking-wide uppercase">
              Kontaktujte nás
            </span>
            <h1 className="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
              Máte otázky? <br />
              <span className="text-blue-600">Sme tu pre vás.</span>
            </h1>
            <p className="mt-4 text-lg text-slate-600 leading-relaxed">
              Zaujímajú vás naše služby alebo potrebujete poradiť s projektom?
              Vyplňte formulár a náš tím expertov vás bude čo najskôr kontaktovať.
            </p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div className="flex items-start gap-4">
              <div className="p-3 bg-white rounded-lg shadow-sm border border-slate-100 text-blue-600">
                <Phone size={24} />
              </div>
              <div>
                <h3 className="font-semibold text-slate-900">Zavolajte nám</h3>
                <p className="text-slate-500 text-sm mt-1">Po-Pia 8:00 - 17:00</p>
                <a href="tel:+421900000000" className="text-blue-600 text-sm font-medium hover:underline">+421 900 000 000</a>
              </div>
            </div>

            <div className="flex items-start gap-4">
              <div className="p-3 bg-white rounded-lg shadow-sm border border-slate-100 text-blue-600">
                <Mail size={24} />
              </div>
              <div>
                <h3 className="font-semibold text-slate-900">Napíšte email</h3>
                <p className="text-slate-500 text-sm mt-1">Odpovedáme do 24h</p>
                <a href="mailto:info@example.com" className="text-blue-600 text-sm font-medium hover:underline">info@example.com</a>
              </div>
            </div>
          </div>
        </div>

        {/* Right Side: The Form */}
        <div className="order-1 lg:order-2 w-full max-w-md mx-auto lg:max-w-full">
          <RequestForm />
        </div>

      </div>

      <SettingsModal isOpen={isSettingsOpen} onClose={() => setIsSettingsOpen(false)} />
    </div>
  );
};

export default App;