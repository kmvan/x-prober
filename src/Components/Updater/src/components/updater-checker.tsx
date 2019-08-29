import { Component } from 'react'
import { observer } from 'mobx-react'
import BootstrapStore from '~components/Bootstrap/src/stores'
import store from '../stores'

@observer
class UpdaterChecker extends Component {
  public componentDidMount() {
    this.check()
  }

  private check = async () => {
    const changelog = await this.getChangelogInfo()

    if (!changelog) {
      return
    }

    const versionInfo = this.getVersionInfo(changelog)

    if (!versionInfo.length) {
      return
    }

    const newVersion = versionInfo[0]

    // no new update
    if (this.versionCompare(BootstrapStore.version, newVersion) !== -1) {
      return
    }

    store.setNewVersion(newVersion)
  }

  private versionCompare(left: string, right: string): boolean | number {
    if (typeof left + typeof right !== 'stringstring') {
      return false
    }

    const a = left.split('.')
    const b = right.split('.')
    const len = Math.max(a.length, b.length)

    for (let i = 0; i < len; i++) {
      if ((a[i] && !b[i] && ~~a[i] > 0) || ~~a[i] > ~~b[i]) {
        return 1
      } else if ((b[i] && !a[i] && ~~b[i] > 0) || ~~a[i] < ~~b[i]) {
        return -1
      }
    }

    return 0
  }

  public getVersionInfo(data: string) {
    const reg = /^#{2}\s+(\d+\.\d+\.\d+)\s+\-\s+(\d{4}\-\d+\-\d+)/gm
    return reg.test(data) ? [RegExp.$1, RegExp.$2] : []
  }

  private getChangelogInfo(): Promise<string> {
    return new Promise(resolve => {
      const xhr = new XMLHttpRequest()
      xhr.open('get', BootstrapStore.changelogUrl)
      xhr.send()
      xhr.addEventListener('load', () => {
        resolve(xhr.responseText)
      })
      xhr.addEventListener('error', () => {
        resolve('')
      })
    })
  }

  public render() {
    return null
  }
}

export default UpdaterChecker
