import { configure, makeAutoObservable } from 'mobx';
import { ConfigStore } from '@/Components/Config/store.ts';
import { gettext } from '@/Components/Language/index.ts';
import { template } from '@/Components/Utils/components/template';

configure({
  enforceActions: 'observed',
});
class Main {
  isUpdating = false;
  isUpdateError = false;
  targetVersion = '';
  constructor() {
    makeAutoObservable(this);
  }
  setTargetVersion = (targetVersion: string) => {
    this.targetVersion = targetVersion;
  };
  setIsUpdating = (isUpdating: boolean) => {
    this.isUpdating = isUpdating;
  };
  setIsUpdateError = (isUpdateError: boolean) => {
    this.isUpdateError = isUpdateError;
  };
  get notiText(): string {
    if (this.isUpdating) {
      return gettext('⏳ Updating, please wait a second...');
    }
    if (this.isUpdateError) {
      return gettext('❌ Update error, click here to try again?');
    }
    if (this.targetVersion) {
      return template(
        gettext('✨ Found new version: {{oldVersion}} ⇢ {{newVersion}}'),
        {
          oldVersion: ConfigStore.pollData?.APP_VERSION ?? '-',
          newVersion: this.targetVersion,
        }
      );
    }
    return '';
  }
}
export const UpdaterStore = new Main();
