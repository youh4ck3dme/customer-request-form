export const submitPlainQuery = async (jsonData: any): Promise<any> => {
  const username = localStorage.getItem('wp_username');
  const appPassword = localStorage.getItem('wp_app_password');

  if (!username || !appPassword) {
    throw new Error('Je potrebné nastaviť API kľúče v nastaveniach.');
  }

  const authString = btoa(`${username}:${appPassword}`);
  const response = await fetch('/wp-json/jet-cct/queries/', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Basic ${authString}`,
    },
    body: JSON.stringify(jsonData),
  });

  if (!response.ok) {
    const errorData = await response.json().catch(() => ({}));
    console.error('API Error Response:', errorData);
    throw new Error(errorData.message || `Chyba servera: ${response.status}`);
  }
  return await response.json();
};
