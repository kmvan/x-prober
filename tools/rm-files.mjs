import { existsSync, readdirSync, rmdirSync, statSync, unlinkSync } from 'fs'
import path from 'path'
/**
 * Remove files
 * @param {string} dir
 */
export const rmFiles = (dir) => {
  if (!existsSync(dir)) {
    return
  }
  const files = readdirSync(dir)
  for (const file of files) {
    const filePath = path.join(dir, file)
    if (statSync(filePath).isDirectory()) {
      rmFiles(filePath)
    } else {
      unlinkSync(filePath)
    }
  }
  rmdirSync(dir)
}
