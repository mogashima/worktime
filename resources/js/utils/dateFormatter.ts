export class DateFormatter {
    /**
     * 文字列またはDateを「〇年〇月〇日」の形式にフォーマットする
     * @param input Date または string
     * @param timezoneOffset 分単位のオフセット（例: +9時間なら 9*60 = 540）
     * @returns フォーマット済み文字列
     */
    static formatDate(
        input: string | Date,
        timezoneOffset: number = 9 * 60
    ): string {
        let date: Date;

        // stringならDateに変換
        if (typeof input === "string") {
            date = new Date(input);
        } else {
            date = input;
        }

        // タイムゾーン補正 (UTC時間にオフセットを加える)
        const utc = date.getTime() + date.getTimezoneOffset() * 60000;
        const localTime = new Date(utc + timezoneOffset * 60000);

        const year = localTime.getFullYear();
        const month = localTime.getMonth() + 1; // 0始まりなので+1
        const day = localTime.getDate();

        return `${year}年${month}月${day}日`;
    }
}
