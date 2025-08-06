export function isDeepEqual<T>(obj1: T, obj2: T): boolean {
  // 如果是同一个引用或基本数据类型，直接返回 true
  if (obj1 === obj2) return true;
  // 如果有一个是 null 或非对象类型，返回 false
  if (
    typeof obj1 !== 'object' ||
    obj1 === null ||
    typeof obj2 !== 'object' ||
    obj2 === null
  ) {
    return false;
  }
  if (Array.isArray(obj1) && Array.isArray(obj2)) {
    if (obj1.length !== obj2.length) {
      return false;
    }
    for (let i = 0; i < obj1.length; i++) {
      if (!isDeepEqual(obj1[i], obj2[i])) {
        return false;
      }
    }
    return true;
  }
  const keys1 = Object.keys(obj1) as (keyof T)[];
  const keys2 = Object.keys(obj2) as (keyof T)[];
  // 如果属性数量不一样，直接返回 false
  if (keys1.length !== keys2.length) {
    return false;
  }
  for (const key of keys1) {
    // 如果 key 不在 obj2 中，返回 false
    if (!keys2.includes(key)) {
      return false;
    }
    // 递归比较每个属性
    if (!isDeepEqual(obj1[key], obj2[key])) {
      return false;
    }
  }
  return true;
}
