import langs from './data.json' with { type: 'json' };

const langId = navigator.language
  .replace('-', '')
  .replace('_', '')
  .toLowerCase();
export const gettext = (text: string, context = ''): string => {
  const id = `${context ? `${context}|` : ''}${text}`;
  return (
    (langs as Record<string, Record<string, string>>)?.[id]?.[langId] ?? text
  );
};
