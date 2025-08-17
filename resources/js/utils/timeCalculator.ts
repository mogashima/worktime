export class TimeCalculator {
  /**
   * 分を受け取り「h時間i分」の形式で返す
   * @param workValue number (分)
   * @returns string
   */
  static format(workValue: number): string {
    if (isNaN(workValue)) return "0時間0分";

    const hours = Math.floor(workValue / 60);
    const minutes = workValue % 60;

    return `${hours}時間${minutes}分`;
  }

}
