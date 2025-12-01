import React, { useState } from 'react';
import { FormStatus } from '../types';
import { submitPlainQuery } from '../services/apiService';
import { CheckCircle, AlertCircle, Loader2, Send } from 'lucide-react';

export const RequestForm: React.FC = () => {
  const [status, setStatus] = useState<FormStatus>(FormStatus.IDLE);
  const [errorMessage, setErrorMessage] = useState<string>('');
  const [formData, setFormData] = useState({
    query_id: '',
    query_url: window.location.href,
    customer_name: '',
    customer_phone: '',
    customer_email: '',
    customer_message: '',
  });

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setStatus(FormStatus.SUBMITTING);
    setErrorMessage('');
    try {
      await submitPlainQuery(formData);
      setStatus(FormStatus.SUCCESS);
    } catch (error: any) {
      console.error(error);
      setErrorMessage(error.message || 'Nastala chyba pri odosielaní.');
      setStatus(FormStatus.ERROR);
    }
  };

  if (status === FormStatus.SUCCESS) {
    return (
      <div className="bg-white p-8 rounded-2xl shadow-xl border border-slate-100 text-center animate-in fade-in zoom-in duration-300">
        <div className="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
          <CheckCircle size={32} />
        </div>
        <h3 className="text-2xl font-bold text-slate-800 mb-2">Ďakujeme!</h3>
        <p className="text-slate-600 mb-6">Váš dopyt bol úspešne odoslaný.</p>
        <button
          onClick={() => setStatus(FormStatus.IDLE)}
          className="text-blue-600 hover:text-blue-700 font-medium text-sm underline underline-offset-4"
        >
          Odoslať ďalší dopyt
        </button>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="bg-white p-8 rounded-2xl shadow-xl border border-slate-100 flex flex-col gap-6 relative overflow-hidden">
      {status === FormStatus.ERROR && (
        <div className="flex items-start gap-2 text-red-600 bg-red-50 p-3 rounded-md text-sm">
          <AlertCircle size={16} className="mt-0.5 shrink-0" />
          <span>{errorMessage}</span>
        </div>
      )}
      <div>
        <label htmlFor="customer_name" className="block text-sm font-medium text-slate-700 mb-1">Meno</label>
        <input
          type="text"
          id="customer_name"
          name="customer_name"
          value={formData.customer_name}
          onChange={handleChange}
          required
          className="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
      </div>
      <div>
        <label htmlFor="customer_phone" className="block text-sm font-medium text-slate-700 mb-1">Mobil</label>
        <input
          type="tel"
          id="customer_phone"
          name="customer_phone"
          value={formData.customer_phone}
          onChange={handleChange}
          required
          className="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
      </div>
      <div>
        <label htmlFor="customer_email" className="block text-sm font-medium text-slate-700 mb-1">Email</label>
        <input
          type="email"
          id="customer_email"
          name="customer_email"
          value={formData.customer_email}
          onChange={handleChange}
          required
          className="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
      </div>
      <div>
        <label htmlFor="customer_message" className="block text-sm font-medium text-slate-700 mb-1">Správa</label>
        <textarea
          id="customer_message"
          name="customer_message"
          value={formData.customer_message}
          onChange={handleChange}
          maxLength={160}
          placeholder="Limit 160 znakov ..."
          className="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
          rows={4}
        />
      </div>
      <button
        type="submit"
        disabled={status === FormStatus.SUBMITTING}
        className="mt-2 inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-70 disabled:cursor-wait transition-all duration-200"
      >
        {status === FormStatus.SUBMITTING ? (
          <>
            <Loader2 className="mr-2 h-4 w-4 animate-spin" />
            Odosielam...
          </>
        ) : (
          <>
            Odoslať
            <Send className="ml-2 h-4 w-4" />
          </>
        )}
      </button>
    </form>
  );
};