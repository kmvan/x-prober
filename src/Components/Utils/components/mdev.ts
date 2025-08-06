export const calculateMdev = (pingTimes: number[]): number => {
  const sum = pingTimes.reduce((a, b) => a + b, 0);
  const avg = sum / pingTimes.length;
  const squaredDiffs = pingTimes.map((time) => {
    const diff = time - avg;
    return diff * diff;
  });
  const variance = squaredDiffs.reduce((a, b) => a + b, 0) / pingTimes.length;
  return Math.sqrt(variance);
};
