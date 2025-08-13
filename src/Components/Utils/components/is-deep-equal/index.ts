export function isDeepEqual<T>(obj1: T, obj2: T): boolean {
  const seen = new Map<unknown, unknown>();
  return _isDeepEqual(obj1, obj2, seen);
}

function _isDeepEqual(
  a: unknown,
  b: unknown,
  seen: Map<unknown, unknown>
): boolean {
  // 快速检查相同引用
  if (a === b) {
    return true;
  }

  // 处理 NaN 情况
  if (Number.isNaN(a) && Number.isNaN(b)) {
    return true;
  }

  // 类型检查
  const typeA = typeof a;
  const typeB = typeof b;
  if (typeA !== typeB) {
    return false;
  }

  // 处理 null 和基础类型
  if (a === null || b === null || typeA !== 'object') {
    return a === b;
  }

  // 检查循环引用
  if (seen.has(a) && seen.get(a) === b) {
    return true;
  }
  seen.set(a, b);

  // 特殊对象处理
  if (a instanceof Date && b instanceof Date) {
    return a.getTime() === b.getTime();
  }

  if (a instanceof RegExp && b instanceof RegExp) {
    return a.toString() === b.toString();
  }

  if (a instanceof Map && b instanceof Map) {
    if (a.size !== b.size) {
      return false;
    }

    for (const [key, valA] of a) {
      if (!b.has(key)) {
        return false;
      }
      const valB = b.get(key);
      if (!_isDeepEqual(valA, valB, seen)) {
        return false;
      }
    }
    return true;
  }

  if (a instanceof Set && b instanceof Set) {
    if (a.size !== b.size) {
      return false;
    }

    for (const val of a) {
      let found = false;
      for (const bVal of b) {
        if (_isDeepEqual(val, bVal, seen)) {
          found = true;
          break;
        }
      }
      if (!found) {
        return false;
      }
    }
    return true;
  }

  // 数组处理
  if (Array.isArray(a) && Array.isArray(b)) {
    if (a.length !== b.length) {
      return false;
    }
    for (let i = 0; i < a.length; i++) {
      if (!_isDeepEqual(a[i], b[i], seen)) {
        return false;
      }
    }
    return true;
  }

  // 修复类型错误：确保 a 和 b 是对象
  if (
    typeof a !== 'object' ||
    a === null ||
    typeof b !== 'object' ||
    b === null
  ) {
    return false;
  }

  // 普通对象处理 - 现在可以安全使用 Object.keys()
  const keysA = Object.keys(a);
  const keysB = Object.keys(b);

  if (keysA.length !== keysB.length) {
    return false;
  }

  const keysSet = new Set(keysB);
  for (const key of keysA) {
    if (!keysSet.has(key)) {
      return false;
    }

    // 使用类型断言确保访问属性安全
    const valA = (a as Record<string, unknown>)[key];
    const valB = (b as Record<string, unknown>)[key];

    if (!_isDeepEqual(valA, valB, seen)) {
      return false;
    }
  }

  return true;
}
